@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Dispatch</h1><p>Kelola keberangkatan boat dan antrean yang berangkat.</p></div>
<div class="card"><div class="card-body"><h3>Jadwal Keberangkatan</h3><div class="table-responsive"><table class="table"><thead><tr><th>Tanggal</th><th>Jam</th><th>Boat</th><th>Rute</th><th>Status</th></tr></thead><tbody>
@forelse($schedules as $schedule)<tr><td>{{ optional($schedule->departure_date)->format('d/m/Y') }}</td><td>{{ $schedule->departure_time }}</td><td>{{ $schedule->boat?->name ?? '-' }}</td><td>{{ $schedule->route?->code ?? '-' }}</td><td>{{ strtoupper($schedule->status ?? '-') }}</td></tr>@empty<tr><td colspan="5" class="text-muted">Belum ada jadwal.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
