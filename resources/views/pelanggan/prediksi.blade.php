@extends('layouts.app')
@section('title', 'Prediksi servis')
@section('sub', 'Dihitung dari kilometer, rata-rata pemakaian, dan interval servis kendaraan.')
@section('content')
@forelse($garage as $g)@php($p=$g->prediksi())
<div class="card">
  <div class="card-h"><div><h2>{{ $g->name }}</h2><span class="small mute">{{ $g->plate }}</span></div><span class="badge b-{{ $p['status'] }}">{{ $p['status'] }}</span></div>
  <div class="bar {{ $p['status']==='terlambat'?'bad':($p['status']==='mendekati'?'warn':'') }}"><i style="width:{{ $p['progress'] }}%"></i></div>
  <div class="cols c3" style="margin-top:12px">
    <div><span class="small mute">Estimasi servis berikutnya</span><br><b>{{ $p['tanggal']->translatedFormat('d F Y') }}</b> <span class="small mute">({{ $p['hari'] }} hari lagi)</span></div>
    <div><span class="small mute">Target kilometer</span><br><b>{{ number_format($p['target_km'],0,',','.') }} km</b> <span class="small mute">(sisa {{ number_format($p['sisa_km'],0,',','.') }})</span></div>
    <div class="right">@if($p['status']!=='aman')<a class="btn btn-primary btn-sm" href="{{ route('pelanggan.booking') }}">Booking sekarang</a>@endif</div>
  </div>
</div>
@empty<div class="card mute">Tambahkan kendaraan di <a href="{{ route('pelanggan.garasi') }}">garasi digital</a> untuk melihat prediksi.</div>@endforelse
@endsection
