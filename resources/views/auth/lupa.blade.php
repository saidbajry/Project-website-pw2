@extends('layouts.auth')
@section('title', 'Lupa password')
@section('content')
<h1>Lupa kata sandi?</h1>
<p class="mute">Masukkan email akun pelanggan Anda. Kami kirim tautan untuk membuat kata sandi baru (berlaku 60 menit).</p>
<form method="POST" action="{{ route('password.email') }}">@csrf
  <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>@error('email')<div class="err">{{ $message }}</div>@enderror</div>
  <button class="btn btn-primary btn-block">Kirim tautan reset</button>
</form>
<div class="portal-sw"><a href="{{ route('login') }}">← Kembali ke halaman masuk</a></div>
@endsection
