<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class PaymentService
{
    public function __construct(
        private SyncTransactionService $syncTransactionService
    ) {
    }

    public function pay(
        int $ticketId,
        string $method,
        float $amount,
        ?int $cashierId = null,
        ?string $referenceNumber = null,
        ?string $notes = null,
        ?string $deviceCode = null
    ): Payment {
        return DB::transaction(function () use (
            $ticketId,
            $method,
            $amount,
            $cashierId,
            $referenceNumber,
            $notes,
            $deviceCode
        ) {

            $ticket = Ticket::lockForUpdate()->findOrFail($ticketId);

            if ($ticket->status !== 'pending') {
                throw new InvalidArgumentException(
                    'Ticket tidak dapat dibayar.'
                );
            }

            if ($amount < (float) $ticket->total) {
                throw new InvalidArgumentException(
                    'Nominal pembayaran kurang dari total ticket.'
                );
            }

            if (!in_array($method, [
                'cash',
                'transfer',
                'qris',
                'debit',
                'credit_card',
            ])) {
                throw new InvalidArgumentException(
                    'Metode pembayaran tidak valid.'
                );
            }

            if (Payment::where('ticket_id', $ticket->id)->exists()) {
                throw new InvalidArgumentException(
                    'Ticket sudah memiliki pembayaran.'
                );
            }

            // Buat Payment
            $payment = Payment::create([
                'uuid' => (string) Str::uuid(),
                'payment_number' => $this->generatePaymentNumber(),
                'ticket_id' => $ticket->id,
                'amount' => $amount,
                'method' => $method,
                'status' => 'paid',
                'paid_at' => now(),
                'reference_number' => $referenceNumber,
                'cashier_id' => $cashierId,
                'notes' => $notes,
            ]);

            // Update Ticket
            $ticket->update([
                'status' => 'paid',
            ]);

            // Catat transaksi untuk offline sync
            $this->syncTransactionService->record(
                'payment',
                'payment',
                $payment->uuid,
                [
                    'payment_number' => $payment->payment_number,
                    'ticket_uuid' => $ticket->uuid,
                    'amount' => (float) $payment->amount,
                    'method' => $payment->method,
                    'status' => $payment->status,
                ],
                $deviceCode
            );

            return $payment->fresh([
                'ticket',
                'cashier',
            ]);
        });
    }

    private function generatePaymentNumber(): string
    {
        $date = now()->format('Ymd');

        $lastPayment = Payment::whereDate(
            'created_at',
            now()->toDateString()
        )
            ->orderByDesc('id')
            ->first();

        $sequence = $lastPayment
            ? ((int) substr($lastPayment->payment_number, -3)) + 1
            : 1;

        return 'PAY-' . $date . '-' .
            str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }
}

