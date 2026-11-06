@extends('layouts.public')
@section('hero')
@php($banner = $promos->firstWhere('type','banner'))
<section class="hero">
    <div class="wrap"> <span class="eyebrow">Showroom &amp; bengkel resmi</span>
        <h1>{{ $banner->title ?? 'Langkah baru. Standar lebih tinggi.' }}</h1>
        <p>{{ $banner->body ?? 'Pilih mobil baru dengan garansi resmi, simulasi kredit instan, dan bengkel dalam satu tempat.' }}
        </p>
        <form class="hero-search" method="GET" action="{{ route('katalog') }}" role="search">
            <div class="searchbar" style="flex:1;min-width:220px">@include('partials.icon',['n'=>'search'])<input
                    name="q" value="{{ request('q') }}" placeholder="Cari merek atau model, mis. Innova"
                    aria-label="Cari mobil" data-suggest="{{ route('suggest.katalog') }}"></div>
            <button class="btn btn-primary">Cari mobil</button>
        </form>
        <div class="chips">@foreach($brands->take(6) as $b)<a class="chip"
                href="{{ route('katalog',['brand'=>$b]) }}#unit">{{ $b }}</a>@endforeach</div>
    </div>
</section>
<div class="wrap">
    <div class="strip">
        <div><b>{{ $stats['unit'] }}</b><span>Unit tersedia</span></div>
        <div><b>{{ $stats['merek'] }}</b><span>Merek</span></div>
        <div><b>{{ $stats['cabang'] }}</b><span>Cabang bengkel</span></div>
        <div><b>Gratis</b><span>Servis pertama</span></div>
    </div>
</div>
@endsection
@section('content')
@php($banner = $promos->firstWhere('type','banner'))
@if($banner?->photo)
<img src="{{ $banner->photo }}" alt="{{ $banner->title }}"
    style="width:100%;height:auto;display:block;border-radius:16px;margin-bottom:18px">
@endif
@if ($promos->where('type','promo')->count())
<div class="promo-grid">@foreach ($promos->where('type','promo') as $p)
    <div class="card">@if($p->photo)<div class="banner-img" style="margin-bottom:10px"><img src="{{ $p->photo }}"
                alt="{{ $p->title }}" loading="lazy"></div>@endif<b>{{ $p->title }}</b>
        <p class="mute small" style="margin:.2rem 0 0">{{ $p->body }}</p>
    </div>
    @endforeach
</div>
@endif

<div class="sec-h" id="unit">
    <h2>Unit tersedia</h2><span class="mute small">{{ $vehicles->count() }} unit ditemukan</span>
</div>
<form class="filter" method="GET" action="{{ route('katalog') }}">
    <div class="searchbar sg">@include('partials.icon',['n'=>'search'])<input name="q" value="{{ request('q') }}"
            placeholder="Cari merek atau model" aria-label="Cari" data-suggest="{{ route('suggest.katalog') }}"></div>
    <select name="brand" aria-label="Merek">
        <option value="">Semua merek</option>@foreach($brands as $b)<option @selected(request('brand')===$b)>{{ $b }}
        </option>@endforeach
    </select>
    <select name="body" aria-label="Tipe">
        <option value="">Semua tipe</option>@foreach($bodies as $b)<option @selected(request('body')===$b)>{{ $b }}
        </option>@endforeach
    </select>
    <select name="maks" aria-label="Harga maksimal">
        <option value="">Semua harga</option>@foreach([250000000,350000000,500000000,700000000] as $m)<option
            value="{{ $m }}" @selected(request('maks')==$m)>Maks. @rp($m)</option>@endforeach
    </select>
    <button class="btn btn-dark">Terapkan</button>
</form>
<div class="grid-cars">
    @forelse ($vehicles as $v)
    <a class="car" href="{{ route('kendaraan', $v) }}">
        <div class="pic">@if($v->photo)<img src="{{ $v->photo }}" alt="{{ $v->title }}" loading="lazy">@else
            @include('partials.car') @endif
            <span class="tag badge {{ $v->kondisi==='baru' ? 'b-dikerjakan' : '' }}">{{ ucfirst($v->kondisi) }}</span>
        </div>
        <div class="body"><b>{{ $v->brand }} {{ $v->model }}</b><span class="meta">{{ $v->variant }} · {{ $v->year }} ·
                {{ $v->transmission }}</span>
            <span class="price">@rp($v->price)</span><span class="meta">Cicilan mulai
                @rp(\App\Models\Prospect::hitung($v->price, (int)($v->price*0.25), 48))/bln</span>
        </div>
    </a>
    @empty
    <div class="card" style="grid-column:1/-1">Tidak ada unit yang cocok. Ubah filter pencarian Anda.</div>
    @endforelse
</div>

@if ($berita->count())
<div class="sec-h">
    <h2>Berita &amp; info terbaru</h2>
</div>
<div class="news-grid">@foreach ($berita as $b)
    <article class="card">
        @if($b->photo)<div class="banner-img" style="margin-bottom:10px"><img src="{{ $b->photo }}"
                alt="{{ $b->title }}" loading="lazy"></div>@endif
        <b>{{ $b->title }}</b>
        <div class="small mute">{{ $b->created_at->translatedFormat('d F Y') }}</div>
        <p class="mute small" style="margin:.4rem 0 0">{{ $b->body }}</p>
    </article>
    @endforeach
</div>
@endif

<div class="sec-h">
    <h2>Lebih dari sekadar showroom</h2>
</div>
<div class="services">
    <div class="card">@include('partials.icon',['n'=>'calendar'])<h3>Booking servis online</h3>
        <p class="mute small">Pilih cabang, tanggal, dan jam tanpa antre telepon.</p><a
            href="{{ route('pelanggan.booking') }}">Booking sekarang →</a>
    </div>
    <div class="card">@include('partials.icon',['n'=>'clock'])<h3>Prediksi servis</h3>
        <p class="mute small">Kami hitung jadwal servis berikutnya dari kilometer mobil Anda.</p><a
            href="{{ route('pelanggan.prediksi') }}">Lihat prediksi →</a>
    </div>
    <div class="card">@include('partials.icon',['n'=>'wallet'])<h3>Simulasi kredit</h3>
        <p class="mute small">Hitung cicilan langsung di halaman detail unit.</p><a href="#unit">Pilih unit →</a>
    </div>
    <div class="card">@include('partials.icon',['n'=>'shield'])<h3>Garansi resmi</h3>
        <p class="mute small">Unit baru dengan garansi pabrikan dan servis berkala pertama gratis.</p>
    </div>
</div>
@endsection