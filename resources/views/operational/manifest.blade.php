@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Manifest</h1><p>Daftar penumpang yang sudah berangkat dan masih tercatat dalam perjalanan.</p></div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Dalam Perjalanan</div><div class="fs-4 fw-semibold">{{ $queues->where('status', 'on_trip')->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Selesai</div><div class="fs-4 fw-semibold">{{ $queues->where('status', 'completed')->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Total Penumpang</div><div class="fs-4 fw-semibold">{{ $queues->sum('passenger_count') }}</div></div></div></div>
</div>

<div class="card"><div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="mb-1">Daftar Manifest</h5><div class="text-muted small">Data mengikuti status antrean setelah dispatch.</div></div></div>
    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>Antrean</th><th>Tanggal</th><th>Customer</th><th>Boat</th><th>Penumpang</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($queues as $queue)
            <tr>
                <td><strong>{{ $queue->queue_number }}</strong></td>
                <td>{{ optional($queue->queue_date)->format('d/m/Y') }}</td>
                <td>{{ $queue->customer?->name ?? '-' }}</td>
                <td>{{ $queue->boat?->code ?? '-' }}{{ $queue->boat?->name ? ' - '.$queue->boat->name : '' }}</td>
                <td>{{ $queue->passenger_count }} orang</td>
                <td><span class="badge text-bg-{{ $queue->status === 'completed' ? 'success' : 'primary' }}">{{ strtoupper($queue->status) }}</span></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data manifest.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div></div>
@endsection
