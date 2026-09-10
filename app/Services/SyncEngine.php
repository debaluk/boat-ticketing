<?php

namespace App\Services;

use App\Models\SyncTransaction;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncEngine
{
    public function __construct(
        private CentralSyncClient $centralSyncClient
    ) {
    }

    /**
     * Proses semua transaksi pending.
     */
    public function processPending(): int
    {
        $transactions = SyncTransaction::where('status', 'pending')
            ->orderBy('id')
            ->get();

        $processed = 0;

        foreach ($transactions as $transaction) {
            if ($this->process($transaction)) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Proses satu transaksi sync.
     */
    public function process(SyncTransaction $transaction): bool
    {
        try {

            /*
             * Tandai attempt terlebih dahulu.
             */
            $transaction->update([
                'attempts' => $transaction->attempts + 1,
                'last_attempt_at' => now(),
            ]);

            /*
             * Kirim transaksi ke Central API.
             */
            $this->centralSyncClient->send($transaction);

            /*
             * Central menerima transaksi.
             */
            $transaction->update([
                'status' => 'synced',
                'synced_at' => now(),
                'error_message' => null,
            ]);

            return true;

        } catch (Throwable $e) {

            $transaction->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Retry transaksi yang gagal.
     */
    public function retryFailed(): int
    {
        $transactions = SyncTransaction::where('status', 'failed')
            ->orderBy('id')
            ->get();

        $processed = 0;

        foreach ($transactions as $transaction) {

            $transaction->update([
                'status' => 'pending',
            ]);

            if ($this->process($transaction)) {
                $processed++;
            }
        }

        return $processed;
    }

    /**
     * Simulasi kegagalan sync untuk testing.
     */
    public function simulateFailure(SyncTransaction $transaction): bool
    {
        try {

            return DB::transaction(function () use ($transaction) {

                $transaction->update([
                    'attempts' => $transaction->attempts + 1,
                    'last_attempt_at' => now(),
                    'status' => 'failed',
                    'error_message' => 'SIMULATED_SYNC_FAILURE',
                ]);

                return false;
            });

        } catch (Throwable $e) {

            $transaction->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}