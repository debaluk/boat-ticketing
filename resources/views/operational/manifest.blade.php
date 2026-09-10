@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Manifest</h1><p>Daftar penumpang berdasarkan antrean dan perjalanan.</p></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Antrean</th><th>Customer</th><th>Boat</th><th>Penumpang</th><th>Status</th></tr></thead><tbody>
@forelse($queues as $queue)<tr><td><strong>{{ $queue->queue_number }}</strong></td><td>{{ $queue->customer?->name ?? '-' }}</td><td>{{ $queue->boat?->name ?? '-' }}</td><td>{{ $queue->passenger_count }}</td><td>{{ strtoupper($queue->status) }}</td></tr>@empty<tr><td colspan="5" class="text-muted">Belum ada data manifest.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
