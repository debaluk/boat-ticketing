@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Antrian Boat</h1><p>Antrean FIFO penumpang yang siap dipanggil.</p></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Posisi</th><th>Nomor</th><th>Customer</th><th>Boat</th><th>Penumpang</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($queues as $queue)<tr><td>{{ $queue->queue_position }}</td><td><strong>{{ $queue->queue_number }}</strong></td><td>{{ $queue->customer?->name ?? '-' }}</td><td>{{ $queue->boat?->name ?? '-' }}</td><td>{{ $queue->passenger_count }}</td><td>{{ strtoupper($queue->status) }}</td><td>@if($queue->status === 'waiting')<form method="POST" action="{{ route('operasional.queue.call',$queue) }}">@csrf<button class="btn btn-primary btn-sm">Panggil</button></form>@endif</td></tr>@empty<tr><td colspan="7" class="text-muted">Antrean kosong.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
