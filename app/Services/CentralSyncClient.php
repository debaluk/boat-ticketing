<?php

namespace App\Services;

use App\Models\SyncTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class CentralSyncClient
{
    public function send(SyncTransaction $transaction): array
    {
        $baseUrl = rtrim(
            config(
                'services.central.url',
                env('CENTRAL_API_URL', 'http://127.0.0.1:8000')
            ),
            '/'
        );

        $deviceCode = null;

        if ($transaction->device_id !== null) {
            $deviceCode = DB::connection('central')
                ->table('devices')
                ->where('id', $transaction->device_id)
                ->value('device_code');
        }

        if ($deviceCode === null) {
            throw new RuntimeException(
                'Device code tidak ditemukan untuk device_id: ' .
                $transaction->device_id
            );
        }

        $response = Http::timeout(15)
            ->acceptJson()
            ->post($baseUrl . '/api/sync/transactions', [
                'uuid' => $transaction->uuid,
                'device_id' => $deviceCode,
                'transaction_type' => $transaction->transaction_type,
                'entity_type' => $transaction->entity_type,
                'entity_id' => $transaction->entity_id,
                'payload' => $transaction->payload ?? [],
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        throw new RuntimeException(
            'Central sync gagal: HTTP ' .
            $response->status() .
            ' - ' .
            $response->body()
        );
    }
}