@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Check-in / Scan</h1><p>Validasi penumpang sebelum masuk antrean boarding.</p></div>
<div class="card"><div class="card-body">
@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
<div class="table-responsive"><table class="table"><thead><tr><th>Antrean</th><th>Customer</th><th>Service</th><th>Boat</th><th>Penumpang</th><th>Status</th></tr></thead><tbody>
@forelse($queues as $queue)<tr><td><strong>{{ $queue->queue_number }}</strong></td><td>{{ $queue->customer?->name ?? '-' }}</td><td>{{ $queue->service?->name ?? '-' }}</td><td>{{ $queue->boat?->name ?? 'Belum dialokasikan' }}</td><td>{{ $queue->passenger_count }}</td><td>{{ strtoupper($queue->status) }}</td></tr>@empty<tr><td colspan="6" class="text-muted">Belum ada antrean aktif.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
