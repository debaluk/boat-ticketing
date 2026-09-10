@extends('layouts.app')
@section('content')
<div class="page-head">
    <h1>Pooling</h1>
    <p>Alokasikan penumpang ke boat sebelum antrean dipanggil.</p>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Menunggu Pooling</div><div class="fs-4 fw-semibold">{{ $queues->where('status', 'waiting')->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Sudah Dialokasikan</div><div class="fs-4 fw-semibold">{{ $queues->whereNotNull('boat_id')->count() }}</div></div></div></div>
    <div class="col-md-4"><div class="card"><div class="card-body"><div class="text-muted small">Penumpang</div><div class="fs-4 fw-semibold">{{ $queues->sum('passenger_count') }}</div></div></div></div>
</div>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div><h5 class="mb-1">Antrean Pooling</h5><div class="text-muted small">Kapasitas pooling mengikuti jumlah penumpang pada tiket.</div></div>
        </div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Posisi</th><th>Antrean</th><th>Customer</th><th>Penumpang</th><th>Boat</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                <tbody>
                @forelse($queues as $queue)
                    <tr>
                        <td>{{ $queue->queue_position }}</td>
                        <td><strong>{{ $queue->queue_number }}</strong></td>
                        <td>{{ $queue->customer?->name ?? '-' }}</td>
                        <td><span class="badge text-bg-light">{{ $queue->passenger_count }} orang</span></td>
                        <td>
                            <form method="POST" action="{{ route('operasional.pooling.assign-boat', $queue) }}" class="d-flex gap-2">
                                @csrf
                                <select name="boat_id" class="form-select form-select-sm" required>
                                    <option value="">Pilih boat</option>
                                    @foreach($boats as $boat)
                                        <option value="{{ $boat->id }}" @selected($queue->boat_id == $boat->id)>{{ $boat->code }} - {{ $boat->name }}</option>
                                    @endforeach
                                </select>
                                <button class="btn btn-primary btn-sm">{{ $queue->boat_id ? 'Ubah' : 'Alokasi' }}</button>
                            </form>
                        </td>
                        <td><span class="badge text-bg-{{ $queue->status === 'called' ? 'warning' : 'secondary' }}">{{ strtoupper($queue->status) }}</span></td>
                        <td class="text-end">@if($queue->boat_id)<span class="text-success small">Siap dipanggil</span>@else<span class="text-muted small">Belum dialokasi</span>@endif</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada penumpang yang menunggu pooling.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
