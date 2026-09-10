@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Pooling</h1><p>Kelompokkan penumpang ke boat sesuai kapasitas.</p></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Antrean</th><th>Customer</th><th>Penumpang</th><th>Boat</th><th>Status</th><th></th></tr></thead><tbody>
@forelse($queues as $queue)<tr><td><strong>{{ $queue->queue_number }}</strong></td><td>{{ $queue->customer?->name ?? '-' }}</td><td>{{ $queue->passenger_count }}</td><td><form method="POST" action="{{ route('operasional.pooling.assign-boat',$queue) }}" class="d-flex gap-2">@csrf<select name="boat_id" class="form-select form-select-sm"><option value="">Pilih boat</option>@foreach($boats as $boat)<option value="{{ $boat->id }}" @selected($queue->boat_id == $boat->id)>{{ $boat->code }} - {{ $boat->name }}</option>@endforeach</select><button class="btn btn-primary btn-sm">Alokasi</button></form></td><td>{{ strtoupper($queue->status) }}</td><td></td></tr>@empty<tr><td colspan="6" class="text-muted">Belum ada penumpang yang menunggu pooling.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
