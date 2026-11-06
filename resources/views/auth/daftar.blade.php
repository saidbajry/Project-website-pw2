@extends('layouts.auth')
@section('title', 'Daftar')
@section('content')
<h1>Buat akun pelanggan</h1>
<p class="mute">Simpan kendaraan di garasi digital dan booking servis tanpa telepon.</p>
<form method="POST" action="{{ route('daftar') }}">@csrf
  <div class="field"><label for="name">Nama lengkap</label><input id="name" name="name" value="{{ old('name') }}" autocomplete="name" required>@error('name')<div class="err">{{ $message }}</div>@enderror</div>
  <div class="cols c2 f">
    <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required>@error('email')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="phone">WhatsApp</label><input id="phone" name="phone" value="{{ old('phone') }}" inputmode="tel" placeholder="0812xxxxxxx" required>@error('phone')<div class="err">{{ $message }}</div>@enderror</div>
  </div>
  <div class="field"><label for="password">Kata sandi (min. 6 karakter)</label><div class="pw"><input id="password" type="password" name="password" autocomplete="new-password" required><button type="button" data-reveal aria-label="Tampilkan kata sandi">@include('partials.icon',['n'=>'eye'])</button></div>@error('password')<div class="err">{{ $message }}</div>@enderror</div>
  <div class="field"><label for="password_confirmation">Ulangi kata sandi</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
  <button class="btn btn-primary btn-block">Daftar</button>
</form>
<div class="portal-sw"><a href="{{ route('login') }}">Sudah punya akun? Masuk</a><a href="{{ route('katalog') }}">← Beranda</a></div>
@endsection
