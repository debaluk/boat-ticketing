@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Boarding</h1><p>Kelola penumpang yang sudah dipanggil sampai siap berangkat.</p></div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Dipanggil</div><div class="fs-4 fw-semibold">{{ $queues->where('status', 'called')->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Sedang Boarding</div><div class="fs-4 fw-semibold">{{ $queues->where('status', 'boarding')->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Total Penumpang</div><div class="fs-4 fw-semibold">{{ $queues->sum('passenger_count') }}</div></div></div></div>
</div>

<div class="card"><div class="card-body">
    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>Antrean</th><th>Customer</th><th>Boat</th><th>Penumpang</th><th>Status</th><th class="text-end">Action</th></tr></thead>
        <tbody>
        @forelse($queues as $queue)
            <tr>
                <td><strong>{{ $queue->queue_number }}</strong></td>
                <td>{{ $queue->customer?->name ?? '-' }}</td>
                <td>{{ $queue->boat?->code ?? '-' }}{{ $queue->boat?->name ? ' - '.$queue->boat->name : '' }}</td>
                <td>{{ $queue->passenger_count }} orang</td>
                <td><span class="badge text-bg-{{ $queue->status === 'called' ? 'warning' : 'primary' }}">{{ strtoupper($queue->status) }}</span></td>
                <td class="text-end">
                    @if($queue->status === 'called')
                        <form method="POST" action="{{ route('dermaga.boarding.start', $queue) }}" class="d-inline">@csrf<button class="btn btn-primary btn-sm">Mulai Boarding</button></form>
                    @else
                        <span class="text-success small">Sedang boarding</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada antrean boarding.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div></div>
@endsection
