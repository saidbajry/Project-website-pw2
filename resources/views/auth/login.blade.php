@extends('layouts.auth')
@section('title', 'Masuk')
@section('content')
@php($titles = ['customer' => 'Masuk pelanggan', 'staff' => 'Masuk staf & admin', 'owner' => 'Masuk pemilik dealer'])
@php($action = ['customer' => route('login'), 'staff' => route('sim.login'), 'owner' => route('eksekutif.login')][$portal])
<div class="tabs" role="navigation" aria-label="Pilih portal">
  <a href="{{ route('login') }}" class="{{ $portal==='customer'?'on':'' }}">Pelanggan</a>
  <a href="{{ route('sim.login') }}" class="{{ $portal==='staff'?'on':'' }}">Staf</a>
  <a href="{{ route('eksekutif.login') }}" class="{{ $portal==='owner'?'on':'' }}">Pemilik</a>
</div>
<h1>{{ $titles[$portal] }}</h1>
<p class="mute">Gunakan email dan kata sandi akun Anda.</p>
<form method="POST" action="{{ $action }}">@csrf
  <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus>@error('email')<div class="err">{{ $message }}</div>@enderror</div>
  <div class="field"><label for="password">Kata sandi</label><div class="pw"><input id="password" type="password" name="password" autocomplete="current-password" required><button type="button" data-reveal aria-label="Tampilkan kata sandi">@include('partials.icon',['n'=>'eye'])</button></div></div>
  <div class="field" style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap"><label style="font-weight:400;margin:0"><input type="checkbox" name="remember"> Ingat saya</label>
    @if($portal==='customer')<a class="small" href="{{ route('password.request') }}">Lupa password?</a>@endif</div>
  <button class="btn btn-primary btn-block">Masuk</button>
</form>
<div class="portal-sw">@if($portal==='customer')<a href="{{ route('daftar') }}">Belum punya akun? Daftar</a>@else<span class="mute">Lupa password? Hubungi pemilik/admin, atau ganti lewat menu Akun setelah masuk.</span>@endif<a href="{{ route('katalog') }}">← Kembali ke beranda</a></div>
@endsection
