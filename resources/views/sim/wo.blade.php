@extends('layouts.app')
@section('title', 'Work order '.$wo->code)
@section('sub', $wo->vehicle->name.' · '.$wo->vehicle->plate.' · pemilik '.$wo->vehicle->user->name)
@section('actions')<a class="btn" href="{{ route('sim.bengkel') }}">Kembali</a>@endsection
@section('content')
<div class="cols c2">
  <div class="card"><h2>Proses servis</h2>
    <form method="POST" action="{{ route('sim.wo.update',$wo) }}">@csrf @method('PATCH')
      <div class="cols c2">
        <div class="field"><label>Mekanik</label><select name="mechanic_id"><option value="">— belum ditugaskan —</option>@foreach($mechanics as $m)<option value="{{ $m->id }}" @selected($wo->mechanic_id==$m->id)>{{ $m->name }}</option>@endforeach</select></div>
        <div class="field"><label>Status</label><select name="status">@foreach(\App\Models\WorkOrder::STATUSES as $s)<option @selected($wo->status===$s)>{{ $s }}</option>@endforeach</select></div>
        <div class="field"><label>Km masuk</label><input value="{{ $wo->km_in }}" disabled></div>
        <div class="field"><label>Km keluar</label><input type="number" name="km_out" value="{{ $wo->km_out }}" min="{{ $wo->km_in }}"></div>
      </div>
      <div class="field"><label>Biaya jasa (Rp)</label><input type="number" name="labor_cost" value="{{ $wo->labor_cost }}" min="0" required></div>
      <div class="field"><label>Catatan</label><textarea name="notes">{{ $wo->notes }}</textarea></div>
      <button class="btn btn-primary">Simpan work order</button>
      <p class="small mute" style="margin-top:8px">Saat status menjadi selesai, kilometer garasi pelanggan, riwayat, dan jadwal prediksi servis diperbarui otomatis.</p>
    </form>
  </div>
  <div class="card"><h2>Suku cadang terpakai</h2>
    <table><thead><tr><th>Item</th><th>Qty</th><th class="right">Subtotal</th></tr></thead><tbody>
    @forelse($wo->parts as $p)<tr><td>{{ $p->part->name }}</td><td>{{ $p->qty }}</td><td class="right">@rp($p->qty*$p->price)</td></tr>@empty<tr><td colspan="3" class="mute">Belum ada suku cadang.</td></tr>@endforelse
    <tr><td colspan="2">Jasa</td><td class="right">@rp($wo->labor_cost)</td></tr>
    <tr><td colspan="2"><b>Total</b></td><td class="right"><b>@rp($wo->total)</b></td></tr></tbody></table>
    <form method="POST" action="{{ route('sim.wo.part',$wo) }}" class="inline-form" style="margin-top:12px">@csrf
      <div class="searchbar" style="flex:1;min-width:180px">@include('partials.icon',['n'=>'search'])<input type="text" id="partq" placeholder="Cari suku cadang (nama/SKU)" aria-label="Cari suku cadang" data-suggest="{{ route('sim.suggest.suku') }}" data-target="#partid" required></div>
      <input type="hidden" name="part_id" id="partid">
      <input type="number" name="qty" value="1" min="1" style="width:76px;flex:none" aria-label="Jumlah"><button class="btn btn-sm">Tambah</button></form>
  </div>
</div>
@endsection
