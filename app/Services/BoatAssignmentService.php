<?php

namespace App\Services;

use App\Models\Boat;
use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class BoatAssignmentService
{
    public function assign(
        int $queueId,
        int $boatId
    ): Queue {
        return DB::transaction(function () use ($queueId, $boatId) {

            $queue = Queue::lockForUpdate()->findOrFail($queueId);

            $boat = Boat::lockForUpdate()->findOrFail($boatId);

            if ($queue->status !== 'waiting') {
                throw new InvalidArgumentException(
                    'Antrean tidak dalam status waiting.'
                );
            }

            if ($boat->status !== 'active') {
                throw new InvalidArgumentException(
                    'Boat tidak aktif.'
                );
            }

            if ($boat->operational_status !== 'available') {
                throw new InvalidArgumentException(
                    'Boat tidak tersedia.'
                );
            }

            if ($queue->passenger_count > $boat->capacity) {
                throw new InvalidArgumentException(
                    'Jumlah penumpang melebihi kapasitas boat.'
                );
            }

            $queue->update([
                'boat_id' => $boat->id,
                'status' => 'called',
            ]);

            $boat->update([
                'operational_status' => 'boarding',
            ]);

            return $queue->fresh([
                'customer',
                'service',
                'boat',
            ]);
        });
    }
}