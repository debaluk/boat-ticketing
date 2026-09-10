<?php

namespace App\Http\Controllers\Pooling;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\Queue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PoolingController extends Controller
{
    public function index()
    {
        $boats = Boat::where('status', 'active')
            ->orderBy('name')
            ->get();

        $queues = Queue::with('customer', 'service')
            ->whereNull('boat_id')
            ->whereIn('status', ['waiting', 'called'])
            ->orderByDesc('priority')
            ->orderBy('queue_position')
            ->get();

        $pooling = $boats->map(function ($boat) {
            $capacity = (int) $boat->capacity;

            $allocated = (int) Queue::where('boat_id', $boat->id)
                ->whereNotIn('status', ['cancelled'])
                ->sum('passenger_count');

            $remaining = max(0, $capacity - $allocated);

            return [
                'boat' => $boat,
                'capacity' => $capacity,
                'allocated' => $allocated,
                'remaining' => $remaining,
                'percentage' => $capacity > 0
                    ? min(100, round(($allocated / $capacity) * 100))
                    : 0,
            ];
        });

        return view('pooling.index', compact(
            'pooling',
            'queues'
        ));
    }

    public function assign(Request $request)
    {
        $data = $request->validate([
            'queue_id' => ['required', 'integer', 'exists:queues,id'],
            'boat_id' => ['required', 'integer', 'exists:boats,id'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $boat = Boat::lockForUpdate()
                    ->findOrFail($data['boat_id']);

                $queue = Queue::lockForUpdate()
                    ->findOrFail($data['queue_id']);

                if (!in_array($queue->status, ['waiting', 'called'], true)) {
                    abort(422, 'Queue tidak dapat dipooling karena statusnya sudah berubah.');
                }

                if ($queue->boat_id !== null) {
                    abort(422, 'Queue sudah masuk ke Boat lain.');
                }

                $capacity = (int) $boat->capacity;

                if ($capacity <= 0) {
                    abort(422, 'Kapasitas Boat belum tersedia.');
                }

                $allocated = (int) Queue::where('boat_id', $boat->id)
                    ->whereNotIn('status', ['cancelled'])
                    ->sum('passenger_count');

                $requested = (int) $queue->passenger_count;

                if (($allocated + $requested) > $capacity) {
                    $remaining = max(0, $capacity - $allocated);

                    abort(
                        422,
                        "Kapasitas tidak mencukupi. Sisa {$remaining} penumpang, sedangkan queue membutuhkan {$requested}."
                    );
                }

                $queue->update([
                    'boat_id' => $boat->id,
                ]);
            });

            return redirect()
                ->route('pooling.index')
                ->with('success', 'Queue berhasil dipooling ke Boat.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'pooling' => $e->getMessage(),
                ]);
        }
    }
}
