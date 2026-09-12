@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Dispatch</h1><p>Verifikasi kesiapan boat dan berangkatkan antrean yang sudah boarding.</p></div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Jadwal Hari Ini / Mendatang</div><div class="fs-4 fw-semibold">{{ $schedules->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Siap Dispatch</div><div class="fs-4 fw-semibold">{{ $queues->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Penumpang Siap</div><div class="fs-4 fw-semibold">{{ $queues->sum('passenger_count') }}</div></div></div></div>
</div>

<div class="card mb-3"><div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h5 class="mb-1">Antrean Siap Berangkat</h5><div class="text-muted small">Hanya antrean dengan status boarding yang dapat di-dispatch.</div></div></div>
    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>Antrean</th><th>Customer</th><th>Boat</th><th>Penumpang</th><th>Status</th><th class="text-end">Action</th></tr></thead>
        <tbody>
        @forelse($queues as $queue)
            <tr>
                <td><strong>{{ $queue->queue_number }}</strong></td>
                <td>{{ $queue->customer?->name ?? '-' }}</td>
                <td>{{ $queue->boat?->code ?? '-' }}{{ $queue->boat?->name ? ' - '.$queue->boat->name : '' }}</td>
                <td>{{ $queue->passenger_count }} orang</td>
                <td><span class="badge text-bg-primary">BOARDING</span></td>
                <td class="text-end"><form method="POST" action="{{ route('dermaga.dispatch.queue', $queue) }}" class="d-inline">@csrf<button class="btn btn-primary btn-sm">Berangkatkan</button></form></td>
            </tr>
        @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada antrean yang siap dispatch.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div></div>

<div class="card"><div class="card-body">
    <h5 class="mb-3">Jadwal Keberangkatan</h5>
    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>Tanggal</th><th>Jam</th><th>Boat</th><th>Rute</th><th>Kapasitas</th><th>Tersedia</th><th>Status</th></tr></thead>
        <tbody>
        @forelse($schedules as $schedule)
            <tr><td>{{ optional($schedule->departure_date)->format('d/m/Y') }}</td><td>{{ $schedule->departure_time }}</td><td>{{ $schedule->boat?->name ?? '-' }}</td><td>{{ $schedule->route?->code ?? '-' }}</td><td>{{ $schedule->capacity }}</td><td>{{ $schedule->available_seats }}</td><td>{{ strtoupper($schedule->status ?? '-') }}</td></tr>
        @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada jadwal.</td></tr>
        @endforelse
        </tbody>
    </table></div>
</div></div>
@endsection
