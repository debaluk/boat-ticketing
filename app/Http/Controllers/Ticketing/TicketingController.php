<?php

namespace App\Http\Controllers\Ticketing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Queue;
use App\Models\Service;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketingController extends Controller
{
    public function index()
    {
        return view('ticketing.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'passenger_count' => ['required', 'integer', 'min:1', 'max:4'],
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
        ]);

        $service = Service::where('status', 'active')
            ->orderBy('id')
            ->firstOrFail();

        return DB::transaction(function () use ($data, $service) {
            $customer = Customer::firstOrCreate(
                ['phone' => $data['customer_phone'] ?: null],
                [
                    'uuid' => (string) Str::uuid(),
                    'code' => 'CUS-' . strtoupper(Str::random(8)),
                    'name' => $data['customer_name'],
                    'phone' => $data['customer_phone'] ?: null,
                    'status' => 'active',
                ]
            );

            if ($customer->name !== $data['customer_name']) {
                $customer->update([
                    'name' => $data['customer_name'],
                ]);
            }

            $today = now()->toDateString();

            $lastPosition = Queue::whereDate('queue_date', $today)
                ->max('queue_position') ?? 0;

            $queue = Queue::create([
                'queue_number' => 'Q-' . now()->format('Ymd') . '-' . str_pad($lastPosition + 1, 4, '0', STR_PAD_LEFT),
                'queue_position' => $lastPosition + 1,
                'priority' => 0,
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'passenger_count' => $data['passenger_count'],
                'queue_date' => $today,
                'queue_time' => now()->format('H:i:s'),
                'status' => 'waiting',
            ]);

            $ticketNumber = 'TK-' . now()->format('Ymd') . '-' .
                str_pad((Ticket::withTrashed()->max('id') ?? 0) + 1, 4, '0', STR_PAD_LEFT);

            Ticket::create([
                'uuid' => (string) Str::uuid(),
                'ticket_number' => $ticketNumber,
                'queue_id' => $queue->id,
                'customer_id' => $customer->id,
                'service_id' => $service->id,
                'passenger_count' => $data['passenger_count'],
                'price' => $service->price,
                'subtotal' => $service->price * $data['passenger_count'],
                'discount' => 0,
                'total' => $service->price * $data['passenger_count'],
                'status' => 'pending',
            ]);

            return redirect()
                ->route('ticketing.index')
                ->with('success', 'Draft tiket berhasil disimpan: ' . $ticketNumber);
        });
    }
}