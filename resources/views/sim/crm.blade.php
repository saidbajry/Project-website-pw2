@extends('layouts.app')
@section('title', 'CRM')
@section('sub', 'Prospek dari simulasi kredit dan minat pembelian di website.')
@section('content')
<div class="card no-print" style="display:flex;gap:8px;flex-wrap:wrap">
  <a class="btn btn-sm {{ request('status')?'':'btn-primary' }}" href="{{ route('sim.crm') }}">Semua</a>
  @foreach(\App\Models\Prospect::STATUSES as $s)<a class="btn btn-sm {{ request('status')===$s?'btn-primary':'' }}" href="{{ route('sim.crm',['status'=>$s]) }}">{{ ucfirst($s) }} ({{ $counts[$s] ?? 0 }})</a>@endforeach
</div>
<div class="card table-wrap"><table>
  <thead><tr><th>Calon pembeli</th><th>Unit</th><th>Simulasi</th><th>Status &amp; catatan</th></tr></thead><tbody>
  @forelse($prospects as $p)
  <tr><td><b>{{ $p->name }}</b><div class="small mute">{{ $p->phone }}@if($p->email) · {{ $p->email }}@endif</div><div class="small mute">{{ $p->created_at->diffForHumans() }}</div></td>
    <td>{{ $p->vehicle?->title ?? '-' }}</td>
    <td class="small">DP @rp($p->down_payment)<br>{{ $p->tenor }} bln · @rp($p->installment)/bln</td>
    <td><form method="POST" action="{{ route('sim.crm.update',$p) }}" class="inline-form" style="flex-wrap:wrap">@csrf @method('PATCH')
      <select name="status">@foreach(\App\Models\Prospect::STATUSES as $s)<option @selected($p->status===$s)>{{ $s }}</option>@endforeach</select>
      <input name="notes" value="{{ $p->notes }}" placeholder="Catatan" style="min-width:140px"><button class="btn btn-sm">Simpan</button></form></td></tr>
  @empty<tr><td colspan="4" class="mute">Belum ada prospek.</td></tr>@endforelse</tbody></table></div>
@endsection
