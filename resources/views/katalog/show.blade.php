@extends('layouts.public')
@section('title', $v->title.' — Mejeng Auto')
@section('content')
<div class="detail">
  <div>
    <div class="pic">@if($v->photo)<img src="{{ $v->photo }}" alt="{{ $v->title }}">@else @include('partials.car') @endif</div>
    <div class="specs">
      <div><span>Tahun</span>{{ $v->year }}</div><div><span>Transmisi</span>{{ $v->transmission }}</div>
      <div><span>Bahan bakar</span>{{ $v->fuel }}</div><div><span>Tipe</span>{{ $v->body_type }}</div>
      <div><span>Warna</span>{{ $v->color ?? '-' }}</div><div><span>Kondisi</span>{{ ucfirst($v->kondisi) }}</div>
      <div><span>Kilometer</span>{{ number_format($v->mileage,0,',','.') }} km</div><div><span>Status</span>{{ ucfirst($v->status) }}</div>
    </div>
    <div class="card"><h3>Deskripsi</h3><p>{{ $v->description }}</p></div>
  </div>
  <aside>
    <div class="card simbox">
      <h1>{{ $v->title }}</h1><b style="font-size:1.4rem">@rp($v->price)</b>
      <h3 style="margin-top:14px">Simulasi kredit</h3>
      <form method="POST" action="{{ route('kendaraan.minat', $v) }}">@csrf
        <div class="field"><label for="dp">Uang muka (Rp)</label><input id="dp" type="number" name="down_payment" min="0" max="{{ $v->price }}" step="1000000" value="{{ old('down_payment', (int)($v->price*0.25)) }}" required></div>
        <div class="field"><label for="tenor">Tenor</label><select id="tenor" name="tenor">@foreach(config('mejeng.tenors') as $t)<option value="{{ $t }}" @selected(old('tenor',36)==$t)>{{ $t }} bulan</option>@endforeach</select></div>
        <div class="out">Estimasi cicilan<br><b id="cicilan">-</b><span class="small"> /bulan</span></div>
        <p class="small mute">Bunga flat {{ config('mejeng.kredit_rate')*100 }}% per tahun. Angka final menyesuaikan perusahaan pembiayaan.</p>
        <hr style="border:0;border-top:1px solid var(--line);margin:12px 0">
        <h3>Minati unit ini</h3>
        <div class="field"><label for="name">Nama</label><input id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required></div>
        <div class="field"><label for="phone">WhatsApp</label><input id="phone" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}" required></div>
        <div class="field"><label for="email">Email (opsional)</label><input id="email" type="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}"></div>
        @if ($errors->any())<div class="err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
        <button class="btn btn-primary btn-block" @disabled($v->status!=='tersedia')>Hubungi saya</button>
      </form>
    </div>
  </aside>
</div>
@if ($similar->count())
<div class="sec-h"><h2>Unit serupa</h2></div>
<div class="grid-cars">@foreach($similar as $s)
  <a class="car" href="{{ route('kendaraan',$s) }}"><div class="pic">@if($s->photo)<img src="{{ $s->photo }}" alt="{{ $s->title }}" loading="lazy">@else @include('partials.car') @endif</div><div class="body"><b>{{ $s->brand }} {{ $s->model }}</b><span class="price">@rp($s->price)</span></div></a>
@endforeach</div>
@endif
<script>
(function(){
  var price={{ $v->price }},rate={{ config('mejeng.kredit_rate') }},dp=document.getElementById('dp'),t=document.getElementById('tenor'),o=document.getElementById('cicilan');
  function hit(){var d=Math.min(Math.max(+dp.value||0,0),price),n=+t.value,c=Math.round((price-d)*(1+rate*n/12)/n);o.textContent='Rp '+c.toLocaleString('id-ID');}
  dp.addEventListener('input',hit);t.addEventListener('change',hit);hit();
})();
</script>
@endsection
