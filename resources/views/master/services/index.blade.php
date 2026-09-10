@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Tarif</h1><p>Daftar layanan dan tarif tiket yang digunakan Ticketing.</p></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Kode</th><th>Nama Layanan</th><th>Harga</th><th>Status</th></tr></thead><tbody>
@forelse($services as $service)<tr><td><strong>{{ $service->code }}</strong></td><td>{{ $service->name }}</td><td>Rp {{ number_format((float)$service->price,0,',','.') }}</td><td>{{ strtoupper($service->status) }}</td></tr>@empty<tr><td colspan="4" class="text-muted">Belum ada tarif.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
