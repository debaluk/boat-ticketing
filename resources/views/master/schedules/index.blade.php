@extends('layouts.app')
@section('content')
<div class="page-head"><h1>Trip / Jadwal</h1><p>Daftar jadwal keberangkatan boat.</p></div>
<div class="card"><div class="card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Tanggal</th><th>Jam</th><th>Boat</th><th>Rute</th><th>Harga</th><th>Kapasitas</th><th>Tersedia</th><th>Status</th></tr></thead><tbody>
@forelse($schedules as $schedule)<tr><td>{{ optional($schedule->departure_date)->format('d/m/Y') }}</td><td>{{ $schedule->departure_time }}</td><td>{{ $schedule->boat?->name ?? '-' }}</td><td>{{ $schedule->route?->code ?? '-' }}</td><td>Rp {{ number_format((float)$schedule->price,0,',','.') }}</td><td>{{ $schedule->capacity }}</td><td>{{ $schedule->available_seats }}</td><td>{{ strtoupper($schedule->status ?? '-') }}</td></tr>@empty<tr><td colspan="8" class="text-muted">Belum ada jadwal.</td></tr>@endforelse
</tbody></table></div></div></div>
@endsection
