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
<div class="auth-split">
  <aside class="auth-side">
    <a class="brand" href="{{ route('katalog') }}"><span class="logo">@include('partials.icon',['n'=>'car'])</span>Mejeng<em>Auto</em></a>
    <div>
      <h2>Langkah baru. Standar lebih tinggi.</h2>
      <p>Satu akun untuk membeli, menservis, dan memantau jadwal perawatan mobil Anda. Semua rapi, semua terpantau.</p>
    </div>
    <p class="small" style="color:#8aa5ba">© {{ date('Y') }} Mejeng Auto</p>
  </aside>
  <main class="auth-main">
    <a class="brand" href="{{ route('katalog') }}" style="margin-bottom:22px"><span class="logo">@include('partials.icon',['n'=>'car'])</span>Mejeng<em>Auto</em></a>
    @include('partials.flash')
    @yield('content')
  </main>
</div>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
