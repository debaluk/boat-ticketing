<?php

namespace App\Services;

use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QueueService
{
    public function createQueue(
        int $customerId,
        int $serviceId,
        int $passengerCount = 1
    ): Queue {
        return DB::transaction(function () use (
            $customerId,
            $serviceId,
            $passengerCount
        ) {
            $today = now()->toDateString();

            $lastQueue = Queue::whereDate('queue_date', $today)
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $nextNumber = $lastQueue
                ? ((int) substr($lastQueue->queue_number, -3)) + 1
                : 1;

            $queueNumber = now()->format('Ymd') . '-' .
                str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            $lastPosition = Queue::whereDate('queue_date', $today)
                ->max('queue_position');

            $position = ($lastPosition ?? 0) + 1;

            return Queue::create([
                'uuid' => (string) Str::uuid(),
                'queue_number' => $queueNumber,
                'queue_position' => $position,
                'priority' => 0,
                'customer_id' => $customerId,
                'service_id' => $serviceId,
                'boat_id' => null,
                'passenger_count' => $passengerCount,
                'queue_date' => $today,
                'queue_time' => now()->format('H:i:s'),
                'status' => 'waiting',
            ]);
        });
    }
}