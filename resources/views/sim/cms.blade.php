@extends('layouts.app')
@section('title', 'Manajemen CMS')
@section('sub', 'Kelola katalog kendaraan, banner, promo, dan berita yang tampil di website.')
@section('actions')<a class="btn btn-primary" href="{{ route('sim.cms.kendaraan.baru') }}">Tambah kendaraan</a>@endsection
@section('content')
<div class="card">
  <div class="card-h"><h2>Katalog kendaraan</h2>
    <form method="GET" action="{{ route('sim.cms') }}" class="inline-form" style="min-width:min(100%,360px)" role="search">
      <div class="searchbar" style="flex:1">@include('partials.icon',['n'=>'search'])<input name="q" value="{{ request('q') }}" placeholder="Cari merek atau model" aria-label="Cari kendaraan" data-suggest="{{ route('suggest.katalog',['mode'=>'fill']) }}" data-submit></div>
      <button class="btn btn-sm">Cari</button>
    </form>
  </div>
  <div class="table-wrap"><table><thead><tr><th>Foto</th><th>Unit</th><th>Harga</th><th>Status</th><th></th></tr></thead><tbody>
  @forelse($vehicles as $v)<tr>
    <td><div class="pic thumb">@if($v->photo)<img src="{{ $v->photo }}" alt="" loading="lazy">@else @include('partials.car') @endif</div></td>
    <td><b>{{ $v->title }}</b><div class="small mute">{{ $v->year }} · {{ $v->body_type }} · {{ $v->kondisi }}@if($v->featured) · unggulan @endif</div></td><td>@rp($v->price)</td><td><span class="badge {{ $v->status==='tersedia'?'b-ok':'b-bad' }}">{{ $v->status }}</span></td>
    <td class="right nowrap"><a class="btn btn-sm" href="{{ route('sim.cms.kendaraan.ubah',$v) }}">Ubah</a>
      <form method="POST" action="{{ route('sim.cms.kendaraan.hapus',$v) }}" style="display:inline" onsubmit="return confirm('Hapus unit ini dari katalog?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form></td></tr>
  @empty<tr><td colspan="5" class="mute">Tidak ada kendaraan yang cocok.</td></tr>@endforelse</tbody></table></div>
</div>
<div class="cols c2" style="align-items:start">
  <div class="card"><h2>Banner, promo &amp; berita</h2>
    @forelse($posts as $p)<div class="list-row"><div style="min-width:0">
        @if($p->photo)<div class="banner-img" style="max-width:300px;margin-bottom:8px"><img src="{{ $p->photo }}" alt="" loading="lazy"></div>@endif
        <b>{{ $p->title }}</b> <span class="badge">{{ $p->type }}</span>@unless($p->is_published) <span class="badge b-warn">disembunyikan</span>@endunless<div class="small mute">{{ $p->body }}</div></div>
      <div class="right nowrap"><form method="POST" action="{{ route('sim.cms.post.toggle',$p) }}" style="display:inline">@csrf @method('PATCH')<button class="btn btn-sm">{{ $p->is_published ? 'Sembunyikan' : 'Tayangkan' }}</button></form>
      <form method="POST" action="{{ route('sim.cms.post.hapus',$p) }}" style="display:inline" onsubmit="return confirm('Hapus konten ini?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Hapus</button></form></div></div>
    @empty<p class="mute">Belum ada konten.</p>@endforelse</div>
  <div class="card"><h2>Konten baru</h2>
    <form method="POST" action="{{ route('sim.cms.post.simpan') }}" enctype="multipart/form-data">@csrf
      <div class="field"><label for="ptitle">Judul</label><input id="ptitle" name="title" required></div>
      <div class="field"><label for="ptype">Jenis</label><select id="ptype" name="type"><option>banner</option><option>promo</option><option>berita</option></select></div>
      <div class="field"><label for="pbody">Isi singkat</label><textarea id="pbody" name="body"></textarea></div>
      <div class="field"><label for="pimg">Foto dari komputer/HP (opsional)</label>
        <div class="banner-img" id="pprev" style="max-width:360px;margin-bottom:8px;display:flex;align-items:center;justify-content:center;color:var(--mute);font-size:.82rem"><span>Pratinjau rasio 8:3</span></div>
        <input id="pimg" type="file" name="image" accept="image/jpeg,image/png,image/webp" data-preview="pprev">
        <div class="small mute" style="margin-top:4px">JPG/PNG/WEBP maks. 5 MB. Otomatis dipotong dari tengah ke 8:3 agar semua banner seragam.</div>@error('image')<div class="err">{{ $message }}</div>@enderror</div>
      <button class="btn btn-primary">Terbitkan</button></form></div>
</div>
@endsection
