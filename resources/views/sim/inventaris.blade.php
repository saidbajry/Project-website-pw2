@extends('layouts.app')
@section('title', 'Manajemen inventaris')
@section('sub', 'Stok suku cadang berkurang otomatis saat dipakai di work order; draf PO dibuat saat stok menipis.')
@section('content')
<div class="cols c2" style="align-items:start">
  <div class="card"><div class="card-h"><h2>Stok suku cadang</h2>
    <form method="GET" action="{{ route('sim.inventaris') }}" class="inline-form" style="min-width:min(100%,320px)" role="search">
      <div class="searchbar" style="flex:1">@include('partials.icon',['n'=>'search'])<input name="q" value="{{ request('q') }}" placeholder="Cari nama atau SKU" aria-label="Cari suku cadang" data-suggest="{{ route('sim.suggest.suku') }}" data-submit></div><button class="btn btn-sm">Cari</button></form></div>
  <div class="table-wrap"><table><thead><tr><th>Item</th><th>Stok</th><th class="right">Harga</th><th>Tambah stok</th></tr></thead><tbody>
  @foreach($parts as $p)<tr><td><b>{{ $p->name }}</b><div class="small mute">{{ $p->sku }} · {{ $p->supplier }}</div></td>
    <td><span class="badge {{ $p->isLow() ? 'b-bad' : 'b-ok' }}">{{ $p->stock }}</span><div class="small mute">min {{ $p->min_stock }}</div></td><td class="right">@rp($p->unit_price)</td>
    <td><form method="POST" action="{{ route('sim.inventaris.restok',$p) }}" class="inline-form">@csrf @method('PATCH')<input type="number" name="qty" min="1" value="10" style="width:64px" aria-label="Jumlah"><button class="btn btn-sm">Tambah</button></form></td></tr>@endforeach</tbody></table></div></div>
  <div>
    <div class="card"><h2>Purchase order</h2>
      @forelse($pos as $po)<div class="list-row"><div><b>{{ $po->part->name }}</b><div class="small mute">Qty {{ $po->qty }} · {{ $po->part->supplier }}</div></div>
        <div class="right"><span class="badge b-{{ $po->status }}">{{ $po->status }}</span>
          <form method="POST" action="{{ route('sim.po.aksi',[$po,$po->status==='draf'?'kirim':'terima']) }}" style="margin-top:4px">@csrf @method('PATCH')
            @if($po->status==='draf')<button class="btn btn-sm">Kirim ke supplier</button>@elseif($po->status==='dikirim')<button class="btn btn-sm btn-primary">Barang diterima</button>@endif</form></div></div>
      @empty<p class="mute">Belum ada PO.</p>@endforelse</div>
    <div class="card"><h2>Tambah suku cadang</h2>
      <form method="POST" action="{{ route('sim.inventaris.simpan') }}">@csrf
        <div class="cols c2">
          <div class="field"><label>SKU</label><input name="sku" required></div><div class="field"><label>Nama</label><input name="name" required></div>
          <div class="field"><label>Stok</label><input type="number" name="stock" value="0" required></div><div class="field"><label>Stok minimum</label><input type="number" name="min_stock" value="5" required></div>
          <div class="field"><label>Harga satuan</label><input type="number" name="unit_price" value="0" required></div><div class="field"><label>Supplier</label><input name="supplier"></div>
        </div><button class="btn btn-primary">Simpan</button></form></div>
  </div>
</div>
@endsection
