<?php

namespace App\Http\Controllers;

use App\Models\GarageVehicle;
use App\Models\Part;
use App\Models\Post;
use App\Models\Prospect;
use App\Models\PurchaseOrder;
use App\Models\ServiceBooking;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\WorkOrder;
use App\Models\WorkOrderPart;
use App\Services\ImageService;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    // ---------- Dashboard operasional ----------
    public function dashboard()
    {
        return view('sim.dashboard', [
            'bookingHariIni' => ServiceBooking::whereDate('booking_date', today())->where('status', '!=', 'batal')->count(),
            'bookingMenunggu' => ServiceBooking::where('status', 'menunggu')->count(),
            'woAktif' => WorkOrder::whereIn('status', ['antri', 'dikerjakan'])->count(),
            'prospekBaru' => Prospect::where('status', 'baru')->count(),
            'stokMenipis' => Part::whereColumn('stock', '<=', 'min_stock')->get(),
            'bookings' => ServiceBooking::with(['vehicle', 'branch', 'user'])->where('status', 'menunggu')->orderBy('booking_date')->take(6)->get(),
            'orders' => WorkOrder::with(['vehicle', 'mechanic'])->whereIn('status', ['antri', 'dikerjakan'])->latest()->take(6)->get(),
            'prospects' => Prospect::with('vehicle')->where('status', 'baru')->latest()->take(5)->get(),
        ]);
    }

    // ---------- CRM ----------
    public function crm(Request $r)
    {
        $q = Prospect::with('vehicle')->latest();
        if ($r->filled('status')) $q->where('status', $r->input('status'));
        return view('sim.crm', ['prospects' => $q->limit(100)->get(), 'counts' => Prospect::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status')]);
    }

    public function crmUpdate(Request $r, Prospect $prospect)
    {
        $d = $r->validate(['status' => 'required|in:' . implode(',', Prospect::STATUSES), 'notes' => 'nullable|string|max:500']);
        $prospect->update($d);
        if ($d['status'] === 'deal') $prospect->vehicle?->update(['status' => 'terjual']);
        return back()->with('ok', 'Prospek diperbarui.');
    }

    // ---------- Manajemen bengkel & work order ----------
    public function workshop()
    {
        return view('sim.bengkel', [
            'bookings' => ServiceBooking::with(['vehicle', 'branch', 'user', 'workOrder'])->whereIn('status', ['menunggu', 'dikonfirmasi'])->orderBy('booking_date')->orderBy('booking_time')->get(),
            'orders' => WorkOrder::with(['vehicle', 'mechanic'])->latest()->limit(40)->get(),
        ]);
    }

    public function bookingConfirm(ServiceBooking $booking)
    {
        $booking->update(['status' => 'dikonfirmasi']);
        return back()->with('ok', 'Booking dikonfirmasi.');
    }

    public function woCreate(ServiceBooking $booking)
    {
        if ($booking->workOrder) return redirect()->route('sim.wo.show', $booking->workOrder);

        $kode = 'WO-' . now()->format('ymd') . '-' . str_pad(WorkOrder::whereDate('created_at', today())->count() + 1, 3, '0', STR_PAD_LEFT);
        $wo = WorkOrder::create([
            'code' => $kode,
            'service_booking_id' => $booking->id,
            'garage_vehicle_id' => $booking->garage_vehicle_id,
            'km_in' => $booking->vehicle->current_km,
            'notes' => $booking->complaint,
        ]);
        $booking->update(['status' => 'dikonfirmasi']);
        return redirect()->route('sim.wo.show', $wo)->with('ok', "Work order {$kode} dibuat. Tugaskan mekanik.");
    }

    public function woShow(WorkOrder $wo)
    {
        return view('sim.wo', [
            'wo' => $wo->load(['vehicle.user', 'booking.branch', 'parts.part', 'mechanic']),
            'mechanics' => User::where('role', 'staff')->orderBy('name')->get(),
            'parts' => Part::where('stock', '>', 0)->orderBy('name')->get(),
        ]);
    }

    public function woUpdate(Request $r, WorkOrder $wo)
    {
        $d = $r->validate([
            'mechanic_id' => 'nullable|exists:users,id',
            'status' => 'required|in:' . implode(',', WorkOrder::STATUSES),
            'km_out' => 'nullable|integer|min:' . $wo->km_in,
            'labor_cost' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:1000',
        ]);
        $wo->fill($d);
        if ($d['status'] === 'dikerjakan' && ! $wo->started_at) $wo->started_at = now();

        // Update kilometer & suku cadang -> data garasi pelanggan, riwayat, dan prediksi servis
        if (in_array($d['status'], ['selesai', 'diambil']) && ! $wo->finished_at) {
            $wo->finished_at = now();
            $wo->km_out = $wo->km_out ?: $wo->km_in;
            $g = $wo->vehicle;
            $g->update([
                'current_km' => max($wo->km_out, $g->current_km),
                'last_service_km' => $wo->km_out,
                'last_service_date' => today(),
            ]);
            $wo->booking?->update(['status' => 'selesai']);
        }
        $wo->save();
        return back()->with('ok', 'Work order diperbarui.');
    }

    public function woAddPart(Request $r, WorkOrder $wo)
    {
        $d = $r->validate(['part_id' => 'required|exists:parts,id', 'qty' => 'required|integer|min:1']);
        $part = Part::findOrFail($d['part_id']);
        if ($d['qty'] > $part->stock) {
            return back()->withErrors(['qty' => "Stok {$part->name} hanya {$part->stock}."]);
        }
        WorkOrderPart::create(['work_order_id' => $wo->id, 'part_id' => $part->id, 'qty' => $d['qty'], 'price' => $part->unit_price]);
        $part->decrement('stock', $d['qty']);
        $part->refresh()->buatDrafPoJikaMenipis();
        return back()->with('ok', 'Suku cadang ditambahkan, stok inventaris berkurang.');
    }

    // ---------- Inventaris ----------
    public function inventory(Request $r)
    {
        $s = trim((string) $r->input('q'));
        return view('sim.inventaris', [
            'parts' => Part::when($s !== '', fn ($q) => $q->where(fn ($x) => $x->where('name', 'like', "%{$s}%")->orWhere('sku', 'like', "%{$s}%")))
                ->orderByRaw('stock <= min_stock desc')->orderBy('name')->get(),
            'pos' => PurchaseOrder::with('part')->latest()->limit(30)->get(),
        ]);
    }

    public function partStore(Request $r)
    {
        $d = $r->validate([
            'sku' => 'required|unique:parts,sku', 'name' => 'required|string|max:120',
            'stock' => 'required|integer|min:0', 'min_stock' => 'required|integer|min:0',
            'unit_price' => 'required|integer|min:0', 'supplier' => 'nullable|string|max:120',
        ]);
        Part::create($d)->buatDrafPoJikaMenipis();
        return back()->with('ok', 'Suku cadang ditambahkan.');
    }

    public function restock(Request $r, Part $part)
    {
        $d = $r->validate(['qty' => 'required|integer|min:1']);
        $part->increment('stock', $d['qty']);
        return back()->with('ok', "Stok {$part->name} bertambah {$d['qty']}.");
    }

    public function poAction(PurchaseOrder $po, string $aksi)
    {
        if ($aksi === 'kirim' && $po->status === 'draf') {
            $po->update(['status' => 'dikirim']);
        } elseif ($aksi === 'terima' && $po->status === 'dikirim') {
            $po->part->increment('stock', $po->qty);
            $po->update(['status' => 'diterima']);
        } elseif ($aksi === 'hapus' && $po->status === 'draf') {
            $po->delete();
        } else {
            abort(422, 'Aksi tidak valid untuk status PO ini.');
        }
        return back()->with('ok', 'Purchase order diperbarui.');
    }

    // ---------- CMS ----------
    public function cms(Request $r)
    {
        $s = trim((string) $r->input('q'));
        return view('sim.cms', [
            'vehicles' => Vehicle::when($s !== '', fn ($q) => $q->where(fn ($x) => $x->where('brand', 'like', "%{$s}%")->orWhere('model', 'like', "%{$s}%")->orWhere('variant', 'like', "%{$s}%")))->latest()->get(),
            'posts' => Post::latest()->get(),
        ]);
    }

    public function vehicleForm(?Vehicle $vehicle = null)
    {
        return view('sim.kendaraan-form', ['v' => $vehicle ?? new Vehicle(['year' => date('Y'), 'transmission' => 'Automatic', 'fuel' => 'Bensin', 'kondisi' => 'baru', 'status' => 'tersedia'])]);
    }

    public function vehicleSave(Request $r, ?Vehicle $vehicle = null)
    {
        $d = $r->validate([
            'brand' => 'required|max:50', 'model' => 'required|max:50', 'variant' => 'nullable|max:80',
            'year' => 'required|integer|min:1990|max:' . (date('Y') + 1), 'price' => 'required|integer|min:0',
            'transmission' => 'required|in:Automatic,Manual', 'fuel' => 'required|max:20', 'body_type' => 'required|max:30',
            'color' => 'nullable|max:30', 'mileage' => 'nullable|integer|min:0', 'kondisi' => 'required|in:baru,bekas',
            'status' => 'required|in:tersedia,terjual', 'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], ['image.image' => 'File harus berupa gambar (JPG, PNG, atau WEBP).', 'image.max' => 'Ukuran foto maksimal 5 MB.']);
        $d['mileage'] = $d['mileage'] ?? 0;
        $d['featured'] = $r->boolean('featured');
        unset($d['image']);

        // Foto di-crop tengah ke 4:3 (1200x900) agar tata letak semua kartu seragam.
        if ($r->hasFile('image')) {
            $d['image'] = ImageService::store($r->file('image'), 'vehicles', 1200, 900);
            ImageService::delete($vehicle?->image);
        } elseif ($vehicle && $r->boolean('remove_image')) {
            ImageService::delete($vehicle->image);
            $d['image'] = null;
        }
        $vehicle ? $vehicle->update($d) : Vehicle::create($d);
        return redirect()->route('sim.cms')->with('ok', 'Kendaraan disimpan.');
    }

    public function vehicleDelete(Vehicle $vehicle)
    {
        ImageService::delete($vehicle->image);
        $vehicle->delete();
        return back()->with('ok', 'Kendaraan dihapus dari katalog.');
    }

    public function postStore(Request $r)
    {
        $d = $r->validate([
            'title' => 'required|max:150', 'type' => 'required|in:promo,berita,banner', 'body' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], ['image.image' => 'File harus berupa gambar (JPG, PNG, atau WEBP).', 'image.max' => 'Ukuran foto maksimal 5 MB.']);
        unset($d['image']);
        // Foto banner/promo/berita disimpan utuh (hanya dikecilkan), tidak dipotong.
        if ($r->hasFile('image')) $d['image'] = ImageService::storeFit($r->file('image'), 'posts');
        Post::create($d);
        return back()->with('ok', 'Konten dibuat.');
    }

    public function postToggle(Post $post)
    {
        $post->update(['is_published' => ! $post->is_published]);
        return back();
    }

    public function postDelete(Post $post)
    {
        ImageService::delete($post->image);
        $post->delete();
        return back()->with('ok', 'Konten dihapus.');
    }
}