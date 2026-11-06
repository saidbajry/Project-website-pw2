<?php

namespace App\Http\Controllers;

use App\Models\Part;
use App\Models\Prospect;
use App\Models\PurchaseOrder;
use App\Models\WorkOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExecutiveController extends Controller
{
    private function bengkel(Carbon $from, Carbon $to): int
    {
        $labor = WorkOrder::whereNotNull('finished_at')->whereBetween('finished_at', [$from, $to])->sum('labor_cost');
        $parts = DB::table('work_order_parts')->join('work_orders', 'work_orders.id', '=', 'work_order_parts.work_order_id')
            ->whereNotNull('work_orders.finished_at')->whereBetween('work_orders.finished_at', [$from, $to])
            ->sum(DB::raw('work_order_parts.qty * work_order_parts.price'));
        return (int) ($labor + $parts);
    }

    private function penjualan(Carbon $from, Carbon $to): int
    {
        return (int) DB::table('prospects')->join('vehicles', 'vehicles.id', '=', 'prospects.vehicle_id')
            ->where('prospects.status', 'deal')->whereBetween('prospects.updated_at', [$from, $to])->sum('vehicles.price');
    }

    public function dashboard()
    {
        $bulan = collect(range(5, 0))->map(function ($i) {
            $m = now()->subMonths($i);
            $a = $m->copy()->startOfMonth(); $b = $m->copy()->endOfMonth();
            return ['label' => $m->translatedFormat('M'), 'penjualan' => $this->penjualan($a, $b), 'bengkel' => $this->bengkel($a, $b)];
        });
        $max = max(1, $bulan->max(fn ($x) => max($x['penjualan'], $x['bengkel'])));

        $a = now()->startOfMonth(); $b = now()->endOfMonth();
        return view('eksekutif.dashboard', [
            'bulan' => $bulan, 'max' => $max,
            'penjualanBulanIni' => $this->penjualan($a, $b),
            'bengkelBulanIni' => $this->bengkel($a, $b),
            'unitTerjual' => Prospect::where('status', 'deal')->whereBetween('updated_at', [$a, $b])->count(),
            'woSelesai' => WorkOrder::whereBetween('finished_at', [$a, $b])->count(),
            'funnel' => Prospect::selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
            'stokMenipis' => Part::whereColumn('stock', '<=', 'min_stock')->orderBy('stock')->get(),
            'poAktif' => PurchaseOrder::with('part')->whereIn('status', ['draf', 'dikirim'])->latest()->get(),
            'nilaiStok' => (int) Part::sum(DB::raw('stock * unit_price')),
        ]);
    }

    public function report(Request $r)
    {
        $from = Carbon::parse($r->input('dari', now()->startOfMonth()))->startOfDay();
        $to = Carbon::parse($r->input('sampai', now()))->endOfDay();

        return view('eksekutif.laporan', [
            'from' => $from, 'to' => $to,
            'penjualan' => $this->penjualan($from, $to),
            'bengkel' => $this->bengkel($from, $to),
            'deals' => Prospect::with('vehicle')->where('status', 'deal')->whereBetween('updated_at', [$from, $to])->get(),
            'orders' => WorkOrder::with(['vehicle', 'parts', 'mechanic'])->whereNotNull('finished_at')->whereBetween('finished_at', [$from, $to])->latest('finished_at')->get(),
            'parts' => Part::orderBy('name')->get(),
            'funnel' => Prospect::whereBetween('created_at', [$from, $to])->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status'),
        ]);
    }
}
