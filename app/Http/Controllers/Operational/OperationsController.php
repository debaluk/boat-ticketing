<?php

namespace App\Http\Controllers\Operational;

use App\Http\Controllers\Controller;
use App\Models\Boat;
use App\Models\Queue;
use App\Models\Schedule;
use App\Models\Service;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function checkin()
    {
        $queues = Queue::with(['customer', 'service', 'boat'])
            ->whereIn('status', ['waiting', 'called', 'boarding'])
            ->orderBy('queue_position')
            ->get();

        return view('operational.checkin', compact('queues'));
    }

    public function boarding()
    {
        $queues = Queue::with(['customer', 'service', 'boat'])
            ->whereIn('status', ['called', 'boarding'])
            ->orderBy('queue_position')
            ->get();

        return view('operational.boarding', compact('queues'));
    }

    public function dispatch()
    {
        $boats = Boat::query()->where('status', 'active')->orderBy('code')->get();
        $schedules = Schedule::with(['boat', 'route'])
            ->whereDate('departure_date', '>=', today())
            ->orderBy('departure_date')
            ->orderBy('departure_time')
            ->get();

        return view('operational.dispatch', compact('boats', 'schedules'));
    }

    public function manifest()
    {
        $queues = Queue::with(['customer', 'service', 'boat'])
            ->whereIn('status', ['boarding', 'on_trip', 'completed'])
            ->latest('queue_date')
            ->latest('queue_time')
            ->get();

        return view('operational.manifest', compact('queues'));
    }

    public function pooling()
    {
        $queues = Queue::with(['customer', 'service', 'boat'])
            ->whereIn('status', ['waiting', 'called'])
            ->orderByDesc('priority')
            ->orderBy('queue_position')
            ->get();

        $boats = Boat::query()->where('status', 'active')->orderBy('code')->get();

        return view('operational.pooling', compact('queues', 'boats'));
    }

    public function queue()
    {
        $queues = Queue::with(['customer', 'service', 'boat'])
            ->whereIn('status', ['waiting', 'called', 'boarding'])
            ->orderByDesc('priority')
            ->orderBy('queue_position')
            ->get();

        return view('operational.queue', compact('queues'));
    }

    public function exception()
    {
        return view('operational.exception');
    }

    public function tripSchedule()
    {
        $schedules = Schedule::with(['boat', 'route'])
            ->orderByDesc('departure_date')
            ->orderBy('departure_time')
            ->get();

        return view('master.schedules.index', compact('schedules'));
    }

    public function tariff()
    {
        $services = Service::query()->orderBy('code')->get();

        return view('master.services.index', compact('services'));
    }

    public function assignBoat(Request $request, Queue $queue)
    {
        $data = $request->validate(['boat_id' => ['required', 'exists:boats,id']]);
        $queue->update(['boat_id' => $data['boat_id']]);

        return back()->with('success', 'Boat berhasil dialokasikan ke antrean.');
    }

    public function callQueue(Queue $queue)
    {
        $queue->update(['status' => 'called']);
        return back()->with('success', 'Antrean dipanggil.');
    }

    public function startBoarding(Queue $queue)
    {
        $queue->update(['status' => 'boarding']);
        return back()->with('success', 'Antrean masuk proses boarding.');
    }

    public function dispatchQueue(Queue $queue)
    {
        $queue->update(['status' => 'on_trip']);
        return back()->with('success', 'Antrean ditandai berangkat.');
    }
}
