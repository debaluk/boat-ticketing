<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\SyncTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class SyncTransactionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uuid' => ['required', 'uuid'],
            'device_id' => ['required', 'string', 'exists:devices,device_code'],
            'transaction_type' => ['required', 'string', 'max:50'],
            'entity_type' => ['required', 'string', 'max:50'],
            'entity_id' => ['required', 'uuid'],
            'payload' => ['nullable', 'array'],
        ]);

        try {
            return DB::transaction(function () use ($validated) {

                /*
                 * Resolve device code -> device ID
                 *
                 * Client mengirim:
                 * DEVICE-OFFLINE-001
                 *
                 * Database menyimpan:
                 * devices.id = 1
                 */
                $device = Device::where(
                    'device_code',
                    $validated['device_id']
                )->firstOrFail();

                /*
                 * Idempotency:
                 * satu entity hanya boleh memiliki satu
                 * sync transaction.
                 */
                $existing = SyncTransaction::where(
                    'entity_type',
                    $validated['entity_type']
                )
                    ->where(
                        'entity_id',
                        $validated['entity_id']
                    )
                    ->first();

                if ($existing) {
                    return response()->json([
                        'success' => true,
                        'status' => $existing->status,
                        'duplicate' => true,
                        'message' => 'Transaction sudah pernah diterima.',
                        'data' => [
                            'uuid' => $existing->uuid,
                            'device_id' => $existing->device_id,
                            'entity_type' => $existing->entity_type,
                            'entity_id' => $existing->entity_id,
                            'status' => $existing->status,
                        ],
                    ]);
                }

                $transaction = SyncTransaction::create([
                    'uuid' => $validated['uuid'],
                    'device_id' => $device->id,
                    'transaction_type' => $validated['transaction_type'],
                    'entity_type' => $validated['entity_type'],
                    'entity_id' => $validated['entity_id'],
                    'payload' => $validated['payload'] ?? [],
                    'status' => 'synced',
                    'attempts' => 1,
                    'last_attempt_at' => now(),
                    'synced_at' => now(),
                    'error_message' => null,
                ]);

                /*
                 * Update heartbeat device
                 */
                $device->update([
                    'last_seen_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'status' => 'synced',
                    'duplicate' => false,
                    'message' => 'Transaction berhasil diterima.',
                    'data' => [
                        'uuid' => $transaction->uuid,
                        'device_id' => $transaction->device_id,
                        'entity_type' => $transaction->entity_type,
                        'entity_id' => $transaction->entity_id,
                        'status' => $transaction->status,
                    ],
                ], 201);
            });

        } catch (Throwable $e) {

            return response()->json([
                'success' => false,
                'status' => 'failed',
                'message' => 'Transaction gagal diproses.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
