@extends('layouts.app')
@section('title', 'Dashboard operasional')
@section('sub', 'Ringkasan hari ini untuk showroom dan bengkel.')
@section('content')
<div class="cols c4" style="margin-bottom:14px">
  <div class="stat"><b>{{ $bookingHariIni }}</b><span>Booking hari ini</span></div>
  <div class="stat"><b>{{ $bookingMenunggu }}</b><span>Menunggu konfirmasi</span></div>
  <div class="stat dark"><b>{{ $woAktif }}</b><span>Work order aktif</span></div>
  <div class="stat"><b>{{ $prospekBaru }}</b><span>Prospek baru</span></div>
</div>
<div class="cols c2">
  <div>
    <div class="card"><div class="card-h"><h2>Booking menunggu</h2><a class="small" href="{{ route('sim.bengkel') }}">Kelola</a></div>
      @forelse($bookings as $b)<div class="list-row"><div><b>{{ $b->vehicle->name }}</b> <span class="small mute">{{ $b->vehicle->plate }}</span><div class="small mute">{{ $b->service_type }} · {{ $b->booking_date->translatedFormat('d M') }} {{ $b->booking_time }} · {{ $b->branch->name }}</div></div></div>@empty<p class="mute">Tidak ada booking menunggu.</p>@endforelse</div>
    <div class="card"><div class="card-h"><h2>Work order berjalan</h2></div>
      @forelse($orders as $o)<div class="list-row"><div><a href="{{ route('sim.wo.show',$o) }}"><b>{{ $o->code }}</b></a> <span class="small mute">{{ $o->vehicle->name }}</span><div class="small mute">Mekanik: {{ $o->mechanic->name ?? 'belum ditugaskan' }}</div></div><span class="badge b-{{ $o->status }}">{{ $o->status }}</span></div>@empty<p class="mute">Tidak ada work order aktif.</p>@endforelse</div>
  </div>
  <div>
    <div class="card"><div class="card-h"><h2>Prospek baru</h2><a class="small" href="{{ route('sim.crm') }}">CRM</a></div>
      @forelse($prospects as $p)<div class="list-row"><div><b>{{ $p->name }}</b> <span class="small mute">{{ $p->phone }}</span><div class="small mute">{{ $p->vehicle?->title }} · cicilan @rp($p->installment)</div></div></div>@empty<p class="mute">Belum ada prospek baru.</p>@endforelse</div>
    <div class="card"><div class="card-h"><h2>Stok menipis</h2><a class="small" href="{{ route('sim.inventaris') }}">Inventaris</a></div>
      @forelse($stokMenipis as $s)<div class="list-row"><span>{{ $s->name }}</span><span class="badge b-bad">sisa {{ $s->stock }}</span></div>@empty<p class="mute">Semua stok aman.</p>@endforelse</div>
  </div>
</div>
@endsection
