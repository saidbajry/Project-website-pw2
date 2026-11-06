@extends('layouts.app')
@section('title', 'Riwayat servis')
@section('sub', 'Perawatan dan suku cadang yang pernah dipasang.')
@section('content')
@forelse($orders as $o)
<div class="card">
  <div class="card-h"><div><h3>{{ $o->vehicle->name }} · {{ $o->vehicle->plate }}</h3><span class="small mute">{{ $o->code }} · {{ $o->finished_at->translatedFormat('d F Y') }} · {{ number_format($o->km_out,0,',','.') }} km @if($o->mechanic)· Mekanik {{ $o->mechanic->name }}@endif</span></div><b>@rp($o->total)</b></div>
  @if($o->notes)<p class="small">{{ $o->notes }}</p>@endif
  <div class="table-wrap"><table><thead><tr><th>Suku cadang</th><th>Qty</th><th class="right">Subtotal</th></tr></thead><tbody>
    @forelse($o->parts as $p)<tr><td>{{ $p->part->name }}</td><td>{{ $p->qty }}</td><td class="right">@rp($p->qty*$p->price)</td></tr>@empty<tr><td colspan="3" class="mute">Tanpa suku cadang.</td></tr>@endforelse
    <tr><td colspan="2">Jasa</td><td class="right">@rp($o->labor_cost)</td></tr></tbody></table></div>
</div>
@empty<div class="card mute">Belum ada riwayat servis.</div>@endforelse
@endsection
