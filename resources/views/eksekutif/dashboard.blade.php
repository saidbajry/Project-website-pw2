@extends('layouts.app')
@section('title', 'Dashboard eksekutif')
@section('sub', 'Ringkasan bulan '.now()->translatedFormat('F Y').' dari penjualan, bengkel, dan inventaris.')
@section('actions')<a class="btn btn-primary" href="{{ route('eksekutif.laporan') }}">Laporan komprehensif</a>@endsection
@section('content')
<div class="cols c4" style="margin-bottom:14px">
  <div class="stat dark"><b>@rp($penjualanBulanIni)</b><span>Penjualan unit bulan ini</span></div>
  <div class="stat"><b>{{ $unitTerjual }}</b><span>Unit terjual</span></div>
  <div class="stat dark"><b>@rp($bengkelBulanIni)</b><span>Pendapatan bengkel</span></div>
  <div class="stat"><b>{{ $woSelesai }}</b><span>Work order selesai</span></div>
</div>
<div class="cols c2">
  <div class="card"><h2>Tren 6 bulan</h2>
    <div class="chart">@foreach($bulan as $b)<div class="g"><i style="height:{{ max(round($b['penjualan']/$max*100),1) }}%" title="Penjualan @rp($b['penjualan'])"></i><i class="b" style="height:{{ max(round($b['bengkel']/$max*100),1) }}%" title="Bengkel @rp($b['bengkel'])"></i><small>{{ $b['label'] }}</small></div>@endforeach</div>
    <div class="legend"><span><i></i>Penjualan unit</span><span><i class="b"></i>Bengkel</span></div></div>
  <div class="card"><h2>Funnel prospek</h2>
    @php($tot = max($funnel->sum(), 1))
    @foreach(\App\Models\Prospect::STATUSES as $s)<div style="margin-bottom:10px"><div class="small" style="display:flex;justify-content:space-between"><span>{{ ucfirst($s) }}</span><b>{{ $funnel[$s] ?? 0 }}</b></div><div class="bar"><i style="width:{{ round(($funnel[$s] ?? 0)/$tot*100) }}%"></i></div></div>@endforeach</div>
</div>
<div class="cols c2">
  <div class="card"><div class="card-h"><h2>Monitoring inventaris</h2><span class="small mute">Nilai stok @rp($nilaiStok)</span></div>
    @forelse($stokMenipis as $s)<div class="list-row"><span>{{ $s->name }}</span><span class="badge b-bad">sisa {{ $s->stock }} / min {{ $s->min_stock }}</span></div>@empty<p class="mute">Semua stok di atas batas minimum.</p>@endforelse</div>
  <div class="card"><h2>Purchase order berjalan</h2>
    @forelse($poAktif as $po)<div class="list-row"><span>{{ $po->part->name }} × {{ $po->qty }}</span><span class="badge b-{{ $po->status }}">{{ $po->status }}</span></div>@empty<p class="mute">Tidak ada PO berjalan.</p>@endforelse</div>
</div>
@endsection
