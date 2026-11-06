@extends('layouts.app')
@section('title', 'Garasi digital')
@section('sub', 'Data kendaraan dan kilometer menjadi dasar prediksi servis.')
@section('content')
<div class="cols c2">
  <div>
    @forelse($garage as $g)
    <div class="card"><div class="card-h"><div><h3>{{ $g->name }}</h3><span class="small mute">{{ $g->plate }} · {{ $g->year }}</span></div><span class="badge b-{{ $g->prediksi()['status'] }}">{{ $g->prediksi()['status'] }}</span></div>
      <p class="small">Servis terakhir: {{ $g->last_service_date?->translatedFormat('d M Y') ?? '-' }} pada {{ number_format($g->last_service_km,0,',','.') }} km</p>
      <form method="POST" action="{{ route('pelanggan.garasi.km', $g) }}" class="inline-form">@csrf @method('PATCH')
        <input type="number" name="current_km" value="{{ $g->current_km }}" min="{{ $g->current_km }}" aria-label="Kilometer saat ini"><span class="small">km</span><button class="btn btn-sm">Perbarui km</button></form>
    </div>
    @empty<div class="card mute">Garasi masih kosong. Tambahkan kendaraan pertama Anda.</div>@endforelse
  </div>
  <div class="card"><h2>Tambah kendaraan</h2>
    <form method="POST" action="{{ route('pelanggan.garasi.simpan') }}">@csrf
      <div class="cols c2">
        <div class="field"><label>Plat nomor</label><input name="plate" value="{{ old('plate') }}" required></div>
        <div class="field"><label>Merek &amp; model</label><input name="name" value="{{ old('name') }}" required></div>
        <div class="field"><label>Tahun</label><input type="number" name="year" value="{{ old('year') }}"></div>
        <div class="field"><label>Kilometer saat ini</label><input type="number" name="current_km" value="{{ old('current_km', 0) }}" required></div>
        <div class="field"><label>Rata-rata km per bulan</label><input type="number" name="avg_km_per_month" value="{{ old('avg_km_per_month', 1000) }}" required></div>
        <div class="field"><label>Tanggal servis terakhir</label><input type="date" name="last_service_date" value="{{ old('last_service_date') }}"></div>
        <div class="field"><label>Km saat servis terakhir</label><input type="number" name="last_service_km" value="{{ old('last_service_km', 0) }}"></div>
      </div>
      <button class="btn btn-primary">Simpan kendaraan</button>
    </form>
  </div>
</div>
@endsection
