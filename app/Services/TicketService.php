<?php

namespace App\Services;

use App\Models\Queue;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class TicketService
{
    public function createFromQueue(int $queueId): Ticket
    {
        return DB::transaction(function () use ($queueId) {

            $queue = Queue::with([
                'customer',
                'service',
            ])
                ->lockForUpdate()
                ->findOrFail($queueId);

            // Queue harus waiting atau called
            if (!in_array($queue->status, ['waiting', 'called'])) {
                throw new InvalidArgumentException(
                    'Queue tidak dapat dibuatkan ticket.'
                );
            }

            // 1 Queue hanya boleh punya 1 Ticket
            if (Ticket::where('queue_id', $queue->id)->exists()) {
                throw new InvalidArgumentException(
                    'Queue ini sudah memiliki ticket.'
                );
            }

            if (!$queue->service) {
                throw new InvalidArgumentException(
                    'Service pada queue tidak ditemukan.'
                );
            }

            $passengerCount = $queue->passenger_count;

            $price = $queue->service->price;

            $subtotal = $passengerCount * $price;

            $discount = 0;

            $total = $subtotal - $discount;

            $ticketNumber = $this->generateTicketNumber();

            return Ticket::create([
				'uuid'            => (string) Str::uuid(),
				'ticket_number'   => $ticketNumber,
				'queue_id'        => $queue->id,
				'customer_id'     => $queue->customer_id,
				'service_id'      => $queue->service_id,
				'passenger_count' => $passengerCount,
				'price'           => $price,
				'subtotal'        => $subtotal,
				'discount'        => $discount,
				'total'            => $total,
				'status'          => 'pending',
			]);
        });
    }

    private function generateTicketNumber(): string
    {
        $date = now()->format('Ymd');

        $lastTicket = Ticket::whereDate('created_at', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        $sequence = $lastTicket
            ? ((int) substr($lastTicket->ticket_number, -3)) + 1
            : 1;

        return $date . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}