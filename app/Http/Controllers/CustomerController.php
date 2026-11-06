<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\GarageVehicle;
use App\Models\ReminderLog;
use App\Models\ServiceBooking;
use App\Models\WorkOrder;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function dashboard(Request $r)
    {
        $u = $r->user();
        $garage = $u->garage()->get();
        return view('pelanggan.dashboard', [
            'garage' => $garage,
            'bookings' => $u->bookings()->with(['vehicle', 'branch'])->whereIn('status', ['menunggu', 'dikonfirmasi'])->orderBy('booking_date')->get(),
            'reminders' => ReminderLog::whereIn('garage_vehicle_id', $garage->pluck('id'))->latest()->take(4)->get(),
            'prospects' => $u->prospects()->with('vehicle')->latest()->take(3)->get(),
            'history' => WorkOrder::with('vehicle')->whereIn('garage_vehicle_id', $garage->pluck('id'))->whereNotNull('finished_at')->latest('finished_at')->take(3)->get(),
        ]);
    }

    // ----- Garasi digital -----
    public function garage(Request $r)
    {
        return view('pelanggan.garasi', ['garage' => $r->user()->garage()->get()]);
    }

    public function garageStore(Request $r)
    {
        $d = $r->validate([
            'plate' => 'required|string|max:20|unique:garage_vehicles,plate',
            'name' => 'required|string|max:100',
            'year' => 'nullable|integer|min:1980|max:' . (date('Y') + 1),
            'current_km' => 'required|integer|min:0',
            'avg_km_per_month' => 'required|integer|min:1',
            'last_service_date' => 'nullable|date|before_or_equal:today',
            'last_service_km' => 'nullable|integer|min:0',
        ]);
        $d['plate'] = strtoupper($d['plate']);
        $d['last_service_km'] = $d['last_service_km'] ?? 0;
        $r->user()->garage()->create($d);
        return back()->with('ok', 'Kendaraan ditambahkan ke garasi digital.');
    }

    public function garageKm(Request $r, GarageVehicle $garasi)
    {
        abort_unless($garasi->user_id === $r->user()->id, 403);
        $d = $r->validate(['current_km' => 'required|integer|min:' . $garasi->current_km]);
        $garasi->update($d);
        return back()->with('ok', 'Kilometer diperbarui. Prediksi servis dihitung ulang.');
    }

    // ----- Booking servis -----
    public function booking(Request $r)
    {
        return view('pelanggan.booking', [
            'garage' => $r->user()->garage()->get(),
            'branches' => Branch::orderBy('name')->get(),
            'bookings' => $r->user()->bookings()->with(['vehicle', 'branch'])->latest('booking_date')->take(20)->get(),
        ]);
    }

    public function bookingStore(Request $r)
    {
        $d = $r->validate([
            'garage_vehicle_id' => 'required|exists:garage_vehicles,id',
            'branch_id' => 'required|exists:branches,id',
            'service_type' => 'required|in:' . implode(',', config('mejeng.service_types')),
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|in:' . implode(',', config('mejeng.slots')),
            'complaint' => 'nullable|string|max:500',
        ]);
        abort_unless(GarageVehicle::whereKey($d['garage_vehicle_id'])->where('user_id', $r->user()->id)->exists(), 403);

        $terisi = ServiceBooking::where('branch_id', $d['branch_id'])->where('booking_date', $d['booking_date'])
            ->where('booking_time', $d['booking_time'])->where('status', '!=', 'batal')->count();
        if ($terisi >= config('mejeng.slot_capacity')) {
            return back()->withInput()->withErrors(['booking_time' => 'Slot jam ini sudah penuh. Pilih jam atau cabang lain.']);
        }

        $r->user()->bookings()->create($d);
        return redirect()->route('pelanggan.booking')->with('ok', 'Booking terkirim. Tunggu konfirmasi dari bengkel.');
    }

    public function bookingCancel(Request $r, ServiceBooking $booking)
    {
        abort_unless($booking->user_id === $r->user()->id && in_array($booking->status, ['menunggu', 'dikonfirmasi']), 403);
        $booking->update(['status' => 'batal']);
        return back()->with('ok', 'Booking dibatalkan.');
    }

    // ----- Riwayat & prediksi -----
    public function history(Request $r)
    {
        $ids = $r->user()->garage()->pluck('id');
        return view('pelanggan.riwayat', [
            'orders' => WorkOrder::with(['vehicle', 'parts.part', 'mechanic'])->whereIn('garage_vehicle_id', $ids)->whereNotNull('finished_at')->latest('finished_at')->get(),
        ]);
    }

    public function prediction(Request $r)
    {
        return view('pelanggan.prediksi', ['garage' => $r->user()->garage()->get()]);
    }
}
