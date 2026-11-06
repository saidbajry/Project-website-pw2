@extends('layouts.auth')
@section('title', 'Reset password')
@section('content')
<h1>Buat kata sandi baru</h1>
<p class="mute">Gunakan minimal 8 karakter.</p>
<form method="POST" action="{{ route('password.update') }}">@csrf
  <input type="hidden" name="token" value="{{ $token }}">
  <div class="field"><label for="email">Email</label><input id="email" type="email" name="email" value="{{ old('email', $email) }}" required>@error('email')<div class="err">{{ $message }}</div>@enderror</div>
  <div class="field"><label for="password">Kata sandi baru</label><div class="pw"><input id="password" type="password" name="password" autocomplete="new-password" required><button type="button" data-reveal aria-label="Tampilkan kata sandi">@include('partials.icon',['n'=>'eye'])</button></div>@error('password')<div class="err">{{ $message }}</div>@enderror</div>
  <div class="field"><label for="password_confirmation">Ulangi kata sandi baru</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
  <button class="btn btn-primary btn-block">Simpan kata sandi</button>
</form>
@endsection
