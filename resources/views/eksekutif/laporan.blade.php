@extends('layouts.app')
@section('title', 'Laporan komprehensif')
@section('sub', $from->translatedFormat('d F Y').' – '.$to->translatedFormat('d F Y'))
@section('actions')<button class="btn btn-dark" onclick="window.print()">Cetak / simpan PDF</button>@endsection
@section('content')
<form class="card no-print inline-form" method="GET">
  <label for="dari" style="margin:0">Dari</label><input id="dari" type="date" name="dari" value="{{ $from->toDateString() }}">
  <label for="sampai" style="margin:0">Sampai</label><input id="sampai" type="date" name="sampai" value="{{ $to->toDateString() }}"><button class="btn btn-primary">Tampilkan</button></form>
<div class="cols c3" style="margin-bottom:14px">
  <div class="stat dark"><b>@rp($penjualan + $bengkel)</b><span>Total pendapatan</span></div>
  <div class="stat"><b>@rp($penjualan)</b><span>Penjualan unit ({{ $deals->count() }} unit)</span></div>
  <div class="stat"><b>@rp($bengkel)</b><span>Bengkel ({{ $orders->count() }} work order)</span></div>
</div>
<div class="card table-wrap"><h2>Penjualan unit</h2><table><thead><tr><th>Pembeli</th><th>Unit</th><th class="right">Harga</th></tr></thead><tbody>
  @forelse($deals as $d)<tr><td>{{ $d->name }}</td><td>{{ $d->vehicle?->title }}</td><td class="right">@rp($d->vehicle?->price ?? 0)</td></tr>@empty<tr><td colspan="3" class="mute">Tidak ada penjualan pada periode ini.</td></tr>@endforelse</tbody></table></div>
<div class="card table-wrap"><h2>Work order selesai</h2><table><thead><tr><th>Kode</th><th>Kendaraan</th><th>Mekanik</th><th>Selesai</th><th class="right">Total</th></tr></thead><tbody>
  @forelse($orders as $o)<tr><td>{{ $o->code }}</td><td>{{ $o->vehicle->name }}</td><td>{{ $o->mechanic->name ?? '-' }}</td><td>{{ $o->finished_at->translatedFormat('d M Y') }}</td><td class="right">@rp($o->total)</td></tr>@empty<tr><td colspan="5" class="mute">Tidak ada work order pada periode ini.</td></tr>@endforelse</tbody></table></div>
<div class="card table-wrap"><h2>Posisi inventaris</h2><table><thead><tr><th>Item</th><th>Stok</th><th>Minimum</th><th class="right">Nilai</th></tr></thead><tbody>
  @foreach($parts as $p)<tr><td>{{ $p->name }}</td><td><span class="badge {{ $p->isLow()?'b-bad':'b-ok' }}">{{ $p->stock }}</span></td><td>{{ $p->min_stock }}</td><td class="right">@rp($p->stock*$p->unit_price)</td></tr>@endforeach</tbody></table></div>
@endsection
