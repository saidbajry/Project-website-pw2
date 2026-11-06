@extends('layouts.app')
@section('title', 'Ganti password')
@section('sub', 'Ganti kata sandi akun '.auth()->user()->email.'. Gunakan kombinasi yang unik dan sulit ditebak.')
@section('content')
<div class="card" style="max-width:520px">
  <form method="POST" action="{{ route('akun.password.update') }}">@csrf @method('PUT')
    <div class="field"><label for="current_password">Kata sandi saat ini</label><div class="pw"><input id="current_password" type="password" name="current_password" autocomplete="current-password" required><button type="button" data-reveal aria-label="Tampilkan kata sandi">@include('partials.icon',['n'=>'eye'])</button></div>@error('current_password')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="password">Kata sandi baru (min. 8 karakter)</label><div class="pw"><input id="password" type="password" name="password" autocomplete="new-password" required><button type="button" data-reveal aria-label="Tampilkan kata sandi">@include('partials.icon',['n'=>'eye'])</button></div>@error('password')<div class="err">{{ $message }}</div>@enderror</div>
    <div class="field"><label for="password_confirmation">Ulangi kata sandi baru</label><input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" required></div>
    <button class="btn btn-primary">Simpan kata sandi</button>
  </form>
</div>
@endsection
