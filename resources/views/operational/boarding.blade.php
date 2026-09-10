@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Boarding</h1><p>Kelola antrean yang siap naik boat.</p></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Antrean</th><th>Customer</th><th>Boat</th><th>Penumpang</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($queues as $queue)<tr><td><strong>{{ $queue->queue_number }}</strong></td><td>{{ $queue->customer?->name ?? '-' }}</td><td>{{ $queue->boat?->name ?? 'Belum dialokasikan' }}</td><td>{{ $queue->passenger_count }}</td><td>{{ strtoupper($queue->status) }}</td><td>@if($queue->status !== 'boarding')<form method="POST" action="{{ route('dermaga.boarding.start',$queue) }}">@csrf<button class="btn btn-primary btn-sm">Mulai Boarding</button></form>@endif</td></tr>@empty<tr><td colspan="6" class="text-muted">Belum ada antrean boarding.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
