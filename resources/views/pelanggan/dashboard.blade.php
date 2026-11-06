@extends('layouts.app')
@section('title', 'Halo, '.explode(' ', auth()->user()->name)[0])
@section('sub', 'Ringkasan kendaraan dan servis Anda.')
@section('actions')<a class="btn btn-primary" href="{{ route('pelanggan.booking') }}">Booking servis</a>@endsection
@section('content')
<div class="cols c3" style="margin-bottom:14px">
  <div class="stat"><b>{{ $garage->count() }}</b><span>Kendaraan di garasi</span></div>
  <div class="stat dark"><b>{{ $bookings->count() }}</b><span>Booking aktif</span></div>
  <div class="stat"><b>{{ $garage->filter(fn($g)=>$g->prediksi()['status']!=='aman')->count() }}</b><span>Perlu servis segera</span></div>
</div>
<div class="cols c2">
  <div>
    <div class="card"><div class="card-h"><h2>Prediksi servis</h2><a href="{{ route('pelanggan.prediksi') }}" class="small">Detail</a></div>
      @forelse($garage as $g)@php($p=$g->prediksi())
        <div class="list-row"><div><b>{{ $g->name }}</b><div class="small mute">{{ $g->plate }} · servis berikutnya {{ $p['tanggal']->translatedFormat('d M Y') }}</div></div><span class="badge b-{{ $p['status'] }}">{{ $p['status'] }}</span></div>
      @empty<p class="mute">Belum ada kendaraan. <a href="{{ route('pelanggan.garasi') }}">Tambah ke garasi digital</a>.</p>@endforelse
    </div>
    <div class="card"><div class="card-h"><h2>Riwayat terbaru</h2><a href="{{ route('pelanggan.riwayat') }}" class="small">Semua</a></div>
      @forelse($history as $h)<div class="list-row"><div><b>{{ $h->vehicle->name }}</b><div class="small mute">{{ $h->code }} · {{ $h->finished_at->translatedFormat('d M Y') }}</div></div><b>@rp($h->total)</b></div>
      @empty<p class="mute">Belum ada riwayat servis.</p>@endforelse
    </div>
  </div>
  <div>
    <div class="card"><div class="card-h"><h2>Booking aktif</h2></div>
      @forelse($bookings as $b)<div class="list-row"><div><b>{{ $b->service_type }}</b><div class="small mute">{{ $b->vehicle->plate }} · {{ $b->booking_date->translatedFormat('d M') }} {{ $b->booking_time }} · {{ $b->branch->name }}</div></div><span class="badge b-{{ $b->status }}">{{ $b->status }}</span></div>
      @empty<p class="mute">Tidak ada booking aktif.</p>@endforelse
    </div>
    <div class="card"><div class="card-h"><h2>Notifikasi</h2></div>
      @forelse($reminders as $n)<div class="list-row"><div class="small">{{ $n->message }}<div class="mute">{{ $n->created_at->diffForHumans() }}</div></div></div>
      @empty<p class="mute">Pengingat WhatsApp servis akan muncul di sini.</p>@endforelse
    </div>
    <div class="card"><div class="card-h"><h2>Minat pembelian</h2></div>
      @forelse($prospects as $p)<div class="list-row"><div><b>{{ $p->vehicle?->title }}</b><div class="small mute">Cicilan @rp($p->installment)/bln · {{ $p->tenor }} bln</div></div><span class="badge b-{{ $p->status }}">{{ $p->status }}</span></div>
      @empty<p class="mute">Belum ada. <a href="{{ route('katalog') }}">Lihat katalog</a>.</p>@endforelse
    </div>
  </div>
</div>
@endsection
