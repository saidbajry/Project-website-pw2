<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0b1624">
<title>@yield('title', 'Mejeng Auto — Showroom & Bengkel Mobil')</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<header class="topbar"><div class="wrap">
  <a class="brand" href="{{ route('katalog') }}"><span class="logo">@include('partials.icon',['n'=>'car'])</span>Mejeng<em>Auto</em></a>
  <nav aria-label="Navigasi utama">
    <a href="{{ route('katalog') }}" class="{{ request()->routeIs('katalog','kendaraan') ? 'on' : '' }}">Katalog</a>
    <a href="{{ route('pelanggan.booking') }}">Booking servis</a>
    <a href="{{ route('pelanggan.prediksi') }}">Prediksi servis</a>
  </nav>
  <div class="act">
    @auth
      @php($home = ['customer' => 'pelanggan.dashboard', 'staff' => 'sim.dashboard', 'owner' => 'eksekutif.dashboard'][auth()->user()->role])
      <a class="btn btn-sm btn-primary" href="{{ route($home) }}">Portal saya</a>
    @else
      <a class="btn btn-sm btn-ghost" href="{{ route('login') }}">Masuk</a><a class="btn btn-sm btn-primary" href="{{ route('daftar') }}">Daftar</a>
    @endauth
    <button class="menu-btn" type="button" data-toggle="nav" aria-label="Menu" aria-expanded="false">@include('partials.icon',['n'=>'menu'])</button>
  </div>
</div></header>
<main>@hasSection('hero')@yield('hero')@endif<div class="wrap" style="margin-top:18px">@include('partials.flash')@yield('content')</div></main>
<footer class="site"><div class="wrap">
  <div class="cols c3" style="gap:22px">
    <div><a class="brand" href="{{ route('katalog') }}" style="color:#fff"><span class="logo">@include('partials.icon',['n'=>'car'])</span>Mejeng<em style="color:#7fe0ec">Auto</em></a><p style="margin-top:10px">Showroom mobil dan bengkel resmi. Beli, servis, dan rawat dalam satu tempat.</p></div>
    <div><b style="color:#fff">Layanan</b><p><a href="{{ route('katalog') }}">Katalog mobil</a><br><a href="{{ route('pelanggan.booking') }}">Booking servis</a><br><a href="{{ route('pelanggan.prediksi') }}">Prediksi servis</a></p></div>
    <div><b style="color:#fff">Akses internal</b><p><a href="{{ route('sim.login') }}">Login staf</a><br><a href="{{ route('eksekutif.login') }}">Dashboard eksekutif</a></p></div>
  </div>
  <p style="margin-top:18px">© {{ date('Y') }} Mejeng Auto. Hak cipta dilindungi.</p>
</div></footer>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
