<?php

namespace App\Services;

use App\Models\SyncTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SyncTransactionService
{
    public function record(
        string $transactionType,
        string $entityType,
        string $entityId,
        array $payload = [],
        ?string $deviceCode = null
    ): SyncTransaction {

        /*
         * Idempotency:
         * Satu entity hanya boleh memiliki satu
         * SyncTransaction aktif.
         */
        $existing = SyncTransaction::where('entity_type', $entityType)
            ->where('entity_id', $entityId)
            ->first();

        if ($existing) {
            return $existing;
        }

        /*
         * Resolve device_code menjadi devices.id
         *
         * sync_transactions.device_id adalah BIGINT
         * sedangkan device_code adalah VARCHAR.
         */
        $deviceId = null;

        if ($deviceCode !== null) {
            $deviceId = DB::connection('central')
                ->table('devices')
                ->where('device_code', $deviceCode)
                ->value('id');
        }

        return SyncTransaction::create([
            'uuid' => (string) Str::uuid(),
            'device_id' => $deviceId,
            'transaction_type' => $transactionType,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'payload' => $payload,
            'status' => 'pending',
            'attempts' => 0,
        ]);
    }
}