<?php

namespace App\Services;

use App\Models\Boat;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BoatTripService
{
    public function startBoarding(int $queueId): Queue
    {
        return DB::transaction(function () use ($queueId) {

            $queue = Queue::lockForUpdate()->findOrFail($queueId);

            $boat = Boat::lockForUpdate()->findOrFail($queue->boat_id);

            if ($queue->status !== 'called') {
                throw new InvalidArgumentException(
                    'Antrean harus dalam status called.'
                );
            }

            if ($boat->operational_status !== 'boarding') {
                throw new InvalidArgumentException(
                    'Boat tidak dalam status boarding.'
                );
            }

            $queue->update([
                'status' => 'boarding',
            ]);

            return $queue->fresh([
                'customer',
                'service',
                'boat',
            ]);
        });
    }

    public function startTrip(int $queueId): Queue
    {
        return DB::transaction(function () use ($queueId) {

            $queue = Queue::lockForUpdate()->findOrFail($queueId);

            $boat = Boat::lockForUpdate()->findOrFail($queue->boat_id);

            if ($queue->status !== 'boarding') {
                throw new InvalidArgumentException(
                    'Antrean harus dalam status boarding.'
                );
            }

            if ($boat->operational_status !== 'boarding') {
                throw new InvalidArgumentException(
                    'Boat belum siap berangkat.'
                );
            }

            $queue->update([
                'status' => 'on_trip',
            ]);

            $boat->update([
                'operational_status' => 'on_trip',
            ]);

            return $queue->fresh([
                'customer',
                'service',
                'boat',
            ]);
        });
    }

    public function completeTrip(int $queueId): Queue
    {
        return DB::transaction(function () use ($queueId) {

            $queue = Queue::lockForUpdate()->findOrFail($queueId);

            $boat = Boat::lockForUpdate()->findOrFail($queue->boat_id);

            if ($queue->status !== 'on_trip') {
                throw new InvalidArgumentException(
                    'Antrean harus dalam status on_trip.'
                );
            }

            if ($boat->operational_status !== 'on_trip') {
                throw new InvalidArgumentException(
                    'Boat tidak sedang dalam perjalanan.'
                );
            }

            $queue->update([
                'status' => 'completed',
            ]);

            $boat->update([
                'operational_status' => 'available',
            ]);

            return $queue->fresh([
                'customer',
                'service',
                'boat',
            ]);
        });
    }
}