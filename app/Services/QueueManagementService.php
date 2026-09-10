<?php

namespace App\Services;

use App\Models\Queue;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class QueueManagementService
{
    public function reorder(
        int $queueId,
        int $newPosition
    ): void {
        DB::transaction(function () use ($queueId, $newPosition) {

            $queue = Queue::lockForUpdate()->findOrFail($queueId);

            if ($queue->status !== 'waiting') {
                throw new InvalidArgumentException(
                    'Hanya antrean waiting yang dapat diatur.'
                );
            }

            $date = $queue->queue_date->toDateString();

            $maxPosition = Queue::whereDate('queue_date', $date)
                ->where('status', 'waiting')
                ->max('queue_position');

            if ($newPosition < 1 || $newPosition > $maxPosition) {
                throw new InvalidArgumentException(
                    'Posisi antrean tidak valid.'
                );
            }

            $oldPosition = $queue->queue_position;

            if ($oldPosition === $newPosition) {
                return;
            }

            if ($newPosition < $oldPosition) {

                Queue::whereDate('queue_date', $date)
                    ->where('status', 'waiting')
                    ->where('id', '!=', $queue->id)
                    ->whereBetween(
                        'queue_position',
                        [$newPosition, $oldPosition - 1]
                    )
                    ->increment('queue_position');

            } else {

                Queue::whereDate('queue_date', $date)
                    ->where('status', 'waiting')
                    ->where('id', '!=', $queue->id)
                    ->whereBetween(
                        'queue_position',
                        [$oldPosition + 1, $newPosition]
                    )
                    ->decrement('queue_position');
            }

            $queue->update([
                'queue_position' => $newPosition,
            ]);
        });
    }
}