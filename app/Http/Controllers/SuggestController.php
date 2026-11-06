<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class SuggestController extends Controller
{
    private function like(Request $r): string
    {
        return '%' . addcslashes(trim((string) $r->input('q')), '%_\\') . '%';
    }

    /** Saran merek + mobil. mode=fill dipakai di panel internal (mengisi kolom, bukan pindah halaman). */
    public function catalog(Request $r)
    {
        if (mb_strlen(trim((string) $r->input('q'))) < 1) return response()->json([]);
        $like = $this->like($r);
        $fill = $r->input('mode') === 'fill';

        $brands = Vehicle::where('brand', 'like', $like)->select('brand')->distinct()->orderBy('brand')->limit(4)->pluck('brand')
            ->map(fn ($b) => ['group' => 'Merek', 'label' => $b, 'sub' => Vehicle::where('brand', $b)->where('status', 'tersedia')->count() . ' unit tersedia']
                + ($fill ? ['value' => $b] : ['url' => route('katalog', ['brand' => $b])]));

        $cars = Vehicle::query()->when(! $fill, fn ($q) => $q->where('status', 'tersedia'))
            ->where(fn ($x) => $x->where('brand', 'like', $like)->orWhere('model', 'like', $like)->orWhere('variant', 'like', $like))
            ->orderByDesc('featured')->limit(6)->get()
            ->map(fn ($v) => ['group' => 'Mobil', 'label' => $v->title, 'sub' => $v->year . ' · Rp ' . number_format($v->price, 0, ',', '.')]
                + ($fill ? ['value' => $v->title] : ['url' => route('kendaraan', $v)]));

        return response()->json($brands->concat($cars)->values());
    }

    /** Saran suku cadang (khusus staf). */
    public function parts(Request $r)
    {
        if (mb_strlen(trim((string) $r->input('q'))) < 1) return response()->json([]);
        $like = $this->like($r);
        return response()->json(
            Part::where(fn ($x) => $x->where('name', 'like', $like)->orWhere('sku', 'like', $like))
                ->orderBy('name')->limit(8)->get()
                ->map(fn ($p) => ['group' => 'Suku cadang', 'label' => $p->name, 'sub' => "{$p->sku} · stok {$p->stock} · Rp " . number_format($p->unit_price, 0, ',', '.'), 'value' => $p->name, 'id' => $p->id])
        );
    }
}
