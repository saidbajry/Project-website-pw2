@extends('layouts.app')
@section('title', 'Manajemen bengkel')
@section('sub', 'Booking masuk, pembuatan work order, dan penugasan mekanik.')
@section('content')
<div class="card"><div class="card-h"><h2>Data booking masuk</h2></div>
  <div class="table-wrap"><table><thead><tr><th>Pelanggan &amp; kendaraan</th><th>Jadwal</th><th>Servis</th><th>Status</th><th></th></tr></thead><tbody>
  @forelse($bookings as $b)
  <tr><td><b>{{ $b->user->name }}</b><div class="small mute">{{ $b->vehicle->name }} · {{ $b->vehicle->plate }}</div></td>
    <td class="nowrap">{{ $b->booking_date->translatedFormat('d M Y') }} {{ $b->booking_time }}<div class="small mute">{{ $b->branch->name }}</div></td>
    <td>{{ $b->service_type }}<div class="small mute">{{ $b->complaint }}</div></td>
    <td><span class="badge b-{{ $b->status }}">{{ $b->status }}</span></td>
    <td class="right nowrap">
      @if($b->status==='menunggu')<form method="POST" action="{{ route('sim.booking.konfirmasi',$b) }}" style="display:inline">@csrf @method('PATCH')<button class="btn btn-sm">Konfirmasi</button></form>@endif
      @if($b->workOrder)<a class="btn btn-sm" href="{{ route('sim.wo.show',$b->workOrder) }}">Buka WO</a>
      @else<form method="POST" action="{{ route('sim.wo.buat',$b) }}" style="display:inline">@csrf<button class="btn btn-sm btn-primary">Buat work order</button></form>@endif</td></tr>
  @empty<tr><td colspan="5" class="mute">Tidak ada booking menunggu.</td></tr>@endforelse</tbody></table></div></div>
<div class="card"><div class="card-h"><h2>Work order</h2></div>
  <div class="table-wrap"><table><thead><tr><th>Kode</th><th>Kendaraan</th><th>Mekanik</th><th>Status</th><th class="right">Total</th></tr></thead><tbody>
  @forelse($orders as $o)<tr><td><a href="{{ route('sim.wo.show',$o) }}"><b>{{ $o->code }}</b></a></td><td>{{ $o->vehicle->name }}<div class="small mute">{{ $o->vehicle->plate }}</div></td><td>{{ $o->mechanic->name ?? '-' }}</td><td><span class="badge b-{{ $o->status }}">{{ $o->status }}</span></td><td class="right">@rp($o->total)</td></tr>
  @empty<tr><td colspan="5" class="mute">Belum ada work order.</td></tr>@endforelse</tbody></table></div></div>
@endsection
