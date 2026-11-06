<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="theme-color" content="#0b1624">
<title>@yield('title') — Mejeng Auto</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
@php
  $role = auth()->user()->role;
  $menus = [
    'customer' => [['pelanggan.dashboard','Ringkasan','home'],['pelanggan.garasi','Garasi digital','key'],['pelanggan.booking','Booking servis','calendar'],['pelanggan.prediksi','Prediksi servis','clock'],['pelanggan.riwayat','Riwayat servis','list'],['katalog','Katalog kendaraan','car']],
    'staff' => [['sim.dashboard','Dashboard operasional','home'],['sim.crm','CRM','users'],['sim.bengkel','Manajemen bengkel','wrench'],['sim.inventaris','Inventaris','box'],['sim.cms','Manajemen CMS','layout']],
    'owner' => [['eksekutif.dashboard','Dashboard eksekutif','chart'],['eksekutif.laporan','Laporan komprehensif','file']],
  ][$role];
  $menus[] = ['akun.password','Ganti password','lock'];
@endphp
<div class="app">
  <div class="scrim"></div>
  <aside class="side" aria-label="Menu utama">
    <a class="brand" href="{{ route('katalog') }}"><span class="logo">@include('partials.icon',['n'=>'car'])</span>Mejeng<em>Auto</em></a>
    <div class="role">{{ ['customer'=>'Portal pelanggan','staff'=>'SIM internal','owner'=>'Eksekutif'][$role] }} · {{ auth()->user()->name }}</div>
    @foreach ($menus as [$r, $label, $icon])
      <a class="m {{ request()->routeIs($r) ? 'on' : '' }}" href="{{ route($r) }}">@include('partials.icon',['n'=>$icon]) {{ $label }}</a>
    @endforeach
    <div class="sp"></div>
    <form class="out" method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm">@include('partials.icon',['n'=>'logout']) Keluar</button></form>
  </aside>
  <header class="mtop"><button class="menu-btn" type="button" data-toggle="drawer" aria-label="Buka menu">@include('partials.icon',['n'=>'menu'])</button><a class="brand" href="{{ route('katalog') }}">Mejeng<em>Auto</em></a></header>
  <div class="main">
    <div class="head"><div><h1>@yield('title')</h1>@hasSection('sub')<p class="mute">@yield('sub')</p>@endif</div><div class="no-print">@yield('actions')</div></div>
    @include('partials.flash')
    @yield('content')
  </div>
  @if ($role === 'customer')
  <nav class="bnav" aria-label="Navigasi cepat">
    @foreach (array_slice($menus, 0, 4) as [$r, $label, $icon])
      <a href="{{ route($r) }}" class="{{ request()->routeIs($r) ? 'on' : '' }}">@include('partials.icon',['n'=>$icon]){{ explode(' ', $label)[0] }}</a>
    @endforeach
    <button type="button" data-toggle="drawer">@include('partials.icon',['n'=>'menu'])Menu</button>
  </nav>
  @endif
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
