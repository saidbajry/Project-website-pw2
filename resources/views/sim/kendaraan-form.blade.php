@extends('layouts.app')
@section('title', $v->exists ? 'Ubah kendaraan' : 'Tambah kendaraan')
@section('actions')<a class="btn" href="{{ route('sim.cms') }}">Kembali</a>@endsection
@section('content')
<form method="POST" action="{{ $v->exists ? route('sim.cms.kendaraan.simpan',$v) : route('sim.cms.kendaraan.simpan') }}" enctype="multipart/form-data">@csrf
<div class="detail" style="margin-top:0">
  <div class="card">
    <h2>Data unit</h2>
    <div class="cols c3 f">
      @foreach([['brand','Merek','text'],['model','Model','text'],['variant','Varian','text'],['year','Tahun','number'],['price','Harga (Rp)','number'],['color','Warna','text'],['body_type','Tipe (SUV, MPV, ...)','text'],['fuel','Bahan bakar','text'],['mileage','Kilometer','number']] as [$n,$l,$t])
        <div class="field"><label for="{{ $n }}">{{ $l }}</label><input id="{{ $n }}" type="{{ $t }}" name="{{ $n }}" value="{{ old($n, $v->$n) }}" {{ in_array($n,['brand','model','year','price','body_type','fuel']) ? 'required' : '' }}></div>
      @endforeach
      <div class="field"><label for="transmission">Transmisi</label><select id="transmission" name="transmission">@foreach(['Automatic','Manual'] as $o)<option @selected(old('transmission',$v->transmission)===$o)>{{ $o }}</option>@endforeach</select></div>
      <div class="field"><label for="kondisi">Kondisi</label><select id="kondisi" name="kondisi">@foreach(['baru','bekas'] as $o)<option @selected(old('kondisi',$v->kondisi)===$o)>{{ $o }}</option>@endforeach</select></div>
      <div class="field"><label for="status">Status</label><select id="status" name="status">@foreach(['tersedia','terjual'] as $o)<option @selected(old('status',$v->status)===$o)>{{ $o }}</option>@endforeach</select></div>
    </div>
    <div class="field"><label for="description">Deskripsi</label><textarea id="description" name="description">{{ old('description',$v->description) }}</textarea></div>
    <div class="field"><label style="font-weight:400"><input type="checkbox" name="featured" value="1" @checked(old('featured',$v->featured))> Tampilkan sebagai unggulan</label></div>
  </div>
  <aside class="card">
    <h2>Foto kendaraan</h2>
    <div class="frame" id="vprev" style="max-width:100%;margin-bottom:10px">
      @if($v->photo)<img src="{{ $v->photo }}" alt="Foto saat ini">@else<span>Pratinjau foto (rasio 4:3)</span>@endif
    </div>
    <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" data-preview="vprev" aria-label="Pilih foto dari perangkat">
    <div class="small mute" style="margin:6px 0 10px">JPG/PNG/WEBP maks. 5 MB. Foto otomatis dipotong dari tengah ke rasio 4:3 (1200×900) sehingga posisi kiri, kanan, atas, dan bawah selalu sama di semua unit.</div>
    @error('image')<div class="err">{{ $message }}</div>@enderror
    @if($v->exists && $v->image)<label style="font-weight:400"><input type="checkbox" name="remove_image" value="1"> Hapus foto ini</label>@endif
    <button class="btn btn-primary btn-block" style="margin-top:14px">Simpan kendaraan</button>
  </aside>
</div>
</form>
@endsection
