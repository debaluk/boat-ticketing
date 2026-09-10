@extends('layouts.app')

@section('content')
<div class="page-container">

    <div class="page-header">
        <div>
            <h1>Pooling</h1>
            <p>Alokasikan antrean ke Boat berdasarkan kapasitas.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->has('pooling'))
        <div class="alert alert-danger">
            {{ $errors->first('pooling') }}
        </div>
    @endif

    <div class="pooling-grid">

        <section class="card">
            <div class="card-header">
                <div>
                    <h2>Boat Operasional</h2>
                    <p>Pilih Boat untuk memasukkan antrean.</p>
                </div>
            </div>

            @forelse($pooling as $item)
                @php
                    $boat = $item['boat'];
                @endphp

                <div class="pooling-boat">
                    <div class="pooling-boat-main">
                        <div>
                            <strong>{{ $boat->name }}</strong>

                            <div class="muted">
                                {{ $boat->code ?? '-' }}
                                ·
                                Kapasitas {{ $item['capacity'] }} orang
                            </div>
                        </div>

                        <div class="capacity-summary">
                            <strong>
                                {{ $item['allocated'] }} / {{ $item['capacity'] }}
                            </strong>
                            <span>penumpang</span>
                        </div>
                    </div>

                    <div class="capacity-bar">
                        <div
                            class="capacity-bar-fill"
                            style="width: {{ $item['percentage'] }}%"
                        ></div>
                    </div>

                    <div class="pooling-boat-footer">
                        <span>
                            Sisa kapasitas:
                            <strong>{{ $item['remaining'] }}</strong>
                        </span>

                        @if($item['remaining'] <= 0)
                            <span class="badge badge-danger">Penuh</span>
                        @elseif($item['remaining'] <= max(1, ceil($item['capacity'] * 0.2)))
                            <span class="badge badge-warning">Hampir penuh</span>
                        @else
                            <span class="badge badge-success">Tersedia</span>
                        @endif
                    </div>

                    @if($queues->isNotEmpty() && $item['remaining'] > 0)
                        <div class="queue-list">

                            @foreach($queues as $queue)
                                @if($queue->passenger_count <= $item['remaining'])
                                    <div class="queue-row">
                                        <div>
                                            <strong>{{ $queue->queue_number }}</strong>

                                            <div class="muted">
                                                {{ $queue->customer?->name ?? '-' }}
                                                ·
                                                {{ $queue->passenger_count }} penumpang
                                            </div>
                                        </div>

                                        <form
                                            method="POST"
                                            action="{{ route('pooling.assign') }}"
                                        >
                                            @csrf

                                            <input
                                                type="hidden"
                                                name="queue_id"
                                                value="{{ $queue->id }}"
                                            >

                                            <input
                                                type="hidden"
                                                name="boat_id"
                                                value="{{ $boat->id }}"
                                            >

                                            <button type="submit" class="btn btn-primary">
                                                Pool
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            @endforeach

                        </div>
                    @endif
                </div>

            @empty
                <div class="empty-state">
                    Belum ada Boat aktif yang tersedia.
                </div>
            @endforelse
        </section>

        <aside class="card">
            <div class="card-header">
                <div>
                    <h2>Menunggu Pooling</h2>
                    <p>Queue yang belum memiliki Boat.</p>
                </div>

                <strong>{{ $queues->count() }}</strong>
            </div>

            @forelse($queues as $queue)
                <div class="queue-row">
                    <div>
                        <strong>{{ $queue->queue_number }}</strong>

                        <div class="muted">
                            {{ $queue->customer?->name ?? '-' }}
                        </div>
                    </div>

                    <span class="badge">
                        {{ $queue->passenger_count }} org
                    </span>
                </div>
            @empty
                <div class="empty-state">
                    Semua queue sudah memiliki Boat.
                </div>
            @endforelse
        </aside>

    </div>
</div>

<style>
.page-container {
    padding: 24px;
}

.page-header {
    margin-bottom: 20px;
}

.page-header h1 {
    margin: 0 0 5px;
}

.page-header p,
.card-header p,
.muted {
    color: #6b7280;
    margin: 0;
}

.pooling-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(300px, 1fr);
    gap: 20px;
}

.card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 20px;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 15px;
    margin-bottom: 18px;
}

.card-header h2 {
    margin: 0 0 4px;
}

.pooling-boat {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 14px;
}

.pooling-boat-main,
.pooling-boat-footer,
.queue-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
}

.capacity-summary {
    text-align: right;
}

.capacity-summary strong {
    display: block;
    font-size: 18px;
}

.capacity-summary span {
    color: #6b7280;
    font-size: 12px;
}

.capacity-bar {
    height: 8px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
    margin: 14px 0;
}

.capacity-bar-fill {
    height: 100%;
    background: currentColor;
}

.pooling-boat-footer {
    font-size: 13px;
}

.queue-list {
    margin-top: 15px;
    border-top: 1px solid #e5e7eb;
}

.queue-row {
    padding: 12px 0;
    border-bottom: 1px solid #f0f0f0;
}

.queue-row:last-child {
    border-bottom: 0;
}

.btn {
    border: 0;
    border-radius: 8px;
    padding: 8px 14px;
    cursor: pointer;
}

.btn-primary {
    background: #111827;
    color: #fff;
}

.badge {
    display: inline-flex;
    padding: 4px 8px;
    border-radius: 999px;
    font-size: 12px;
    background: #f3f4f6;
}

.badge-success {
    background: #dcfce7;
    color: #166534;
}

.badge-warning {
    background: #fef3c7;
    color: #92400e;
}

.badge-danger {
    background: #fee2e2;
    color: #991b1b;
}

.alert {
    border-radius: 10px;
    padding: 12px 15px;
    margin-bottom: 18px;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
}

.alert-danger {
    background: #fee2e2;
    color: #991b1b;
}

.empty-state {
    padding: 30px 10px;
    text-align: center;
    color: #6b7280;
}

@media (max-width: 900px) {
    .pooling-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection
