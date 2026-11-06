@extends('layouts.app')
@section('title', 'Booking servis')
@section('sub', 'Pilih cabang, tanggal, dan jam. Tiap slot terbatas.')
@section('content')
<div class="cols c2">
  <div class="card"><h2>Booking baru</h2>
    @if($garage->isEmpty())<p class="mute">Tambahkan kendaraan di <a href="{{ route('pelanggan.garasi') }}">garasi digital</a> dahulu.</p>
    @else
    <form method="POST" action="{{ route('pelanggan.booking.simpan') }}">@csrf
      <div class="field"><label>Kendaraan</label><select name="garage_vehicle_id">@foreach($garage as $g)<option value="{{ $g->id }}" @selected(old('garage_vehicle_id')==$g->id)>{{ $g->name }} — {{ $g->plate }}</option>@endforeach</select></div>
      <div class="field"><label>Cabang</label><select name="branch_id">@foreach($branches as $b)<option value="{{ $b->id }}" @selected(old('branch_id')==$b->id)>{{ $b->name }} ({{ $b->city }})</option>@endforeach</select></div>
      <div class="field"><label>Jenis servis</label><select name="service_type">@foreach(config('mejeng.service_types') as $t)<option @selected(old('service_type')===$t)>{{ $t }}</option>@endforeach</select></div>
      <div class="cols c2">
        <div class="field"><label>Tanggal</label><input type="date" name="booking_date" min="{{ today()->toDateString() }}" value="{{ old('booking_date', today()->addDay()->toDateString()) }}" required></div>
        <div class="field"><label>Jam</label><select name="booking_time">@foreach(config('mejeng.slots') as $s)<option @selected(old('booking_time')===$s)>{{ $s }}</option>@endforeach</select></div>
      </div>
      <div class="field"><label>Keluhan (opsional)</label><textarea name="complaint">{{ old('complaint') }}</textarea></div>
      <button class="btn btn-primary">Kirim booking</button>
    </form>@endif
  </div>
  <div class="card"><h2>Booking saya</h2>
    @forelse($bookings as $b)
      <div class="list-row"><div><b>{{ $b->service_type }}</b><div class="small mute">{{ $b->vehicle->plate }} · {{ $b->booking_date->translatedFormat('d M Y') }} {{ $b->booking_time }} · {{ $b->branch->name }}</div></div>
        <div class="right"><span class="badge b-{{ $b->status }}">{{ $b->status }}</span>
        @if(in_array($b->status,['menunggu','dikonfirmasi']))<form method="POST" action="{{ route('pelanggan.booking.batal',$b) }}" onsubmit="return confirm('Batalkan booking ini?')">@csrf @method('PATCH')<button class="btn btn-sm btn-danger" style="margin-top:4px">Batalkan</button></form>@endif</div></div>
    @empty<p class="mute">Belum ada booking.</p>@endforelse
  </div>
</div>
@endsection
