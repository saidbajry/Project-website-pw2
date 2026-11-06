<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Post;
use App\Models\Prospect;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $r)
    {
        $q = Vehicle::query()->where('status', 'tersedia');
        if ($r->filled('q')) {
            $s = $r->input('q');
            $q->where(fn ($x) => $x->where('brand', 'like', "%{$s}%")->orWhere('model', 'like', "%{$s}%"));
        }
        if ($r->filled('body')) $q->where('body_type', $r->input('body'));
        if ($r->filled('brand')) $q->where('brand', $r->input('brand'));
        if ($r->filled('maks')) $q->where('price', '<=', (int) $r->input('maks'));

        return view('katalog.index', [
            'vehicles' => $q->orderByDesc('featured')->latest()->limit(60)->get(),
            'brands' => Vehicle::distinct()->orderBy('brand')->pluck('brand'),
            'bodies' => Vehicle::distinct()->orderBy('body_type')->pluck('body_type'),
            'stats' => ['unit' => Vehicle::where('status', 'tersedia')->count(), 'merek' => Vehicle::distinct()->count('brand'), 'cabang' => Branch::count()],
            'promos' => Post::where('is_published', true)->whereIn('type', ['promo', 'banner'])->latest()->take(3)->get(),
            'berita' => Post::where('is_published', true)->where('type', 'berita')->latest()->take(3)->get(),
        ]);
    }

    public function show(Vehicle $vehicle)
    {
        return view('katalog.show', [
            'v' => $vehicle,
            'similar' => Vehicle::where('body_type', $vehicle->body_type)->where('id', '!=', $vehicle->id)->where('status', 'tersedia')->limit(3)->get(),
        ]);
    }

    /** Simulasi kredit -> informasi/prospek pembelian -> masuk CRM. */
    public function interest(Request $r, Vehicle $vehicle)
    {
        $d = $r->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email',
            'down_payment' => 'required|integer|min:0|max:' . $vehicle->price,
            'tenor' => 'required|in:' . implode(',', config('mejeng.tenors')),
        ]);
        $cicilan = Prospect::hitung($vehicle->price, $d['down_payment'], $d['tenor']);

        Prospect::create($d + [
            'vehicle_id' => $vehicle->id,
            'user_id' => $r->user()?->id,
            'installment' => $cicilan,
        ]);

        return back()->with('ok', 'Terima kasih! Tim sales akan menghubungi Anda. Estimasi cicilan Rp ' . number_format($cicilan, 0, ',', '.') . '/bulan.');
    }
}