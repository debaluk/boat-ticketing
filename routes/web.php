<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Master\BoatController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\AgentController;
use App\Http\Controllers\Ticketing\TicketingController;
use App\Http\Controllers\Operational\OperationsController;
use App\Http\Controllers\Pooling\PoolingController;

/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

Route::get('/login', [LoginController::class, 'showLogin'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [LoginController::class, 'login'])
    ->middleware('guest');

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


/*
|--------------------------------------------------------------------------
| Application Entry
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard.index');
})
    ->middleware(['auth', 'role:superadmin,kasir,agen'])
    ->name('dashboard');


/*
|--------------------------------------------------------------------------
| Dermaga
|--------------------------------------------------------------------------
*/

Route::get('/dermaga/control-center', function () {
    return view('dermaga.control-center');
})
    ->middleware(['auth', 'role:superadmin,petugas_dermaga'])
    ->name('dermaga.control-center');


/*
|--------------------------------------------------------------------------
| System Status
|--------------------------------------------------------------------------
*/

Route::get('/offline-status', function () {
    return response()->json([
        'local'      => 'ONLINE',
        'cloud_sync' => config('offline.sync_enabled')
            ? 'ENABLED'
            : 'DISABLED',
        'device'     => config('offline.device_code'),
        'time'       => now()->toIso8601String(),
    ]);
});


/*
|--------------------------------------------------------------------------
| Ticketing
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:superadmin,kasir,agen'])->group(function () {
    Route::get('/ticketing', [TicketingController::class, 'index'])
        ->name('ticketing.index');

    Route::post('/ticketing', [TicketingController::class, 'store'])
        ->name('ticketing.store');
});


/*
|--------------------------------------------------------------------------
| Operasional / Dermaga
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:superadmin,kasir,agen,petugas_dermaga'])->group(function () {

    Route::get('/dermaga/checkin', [OperationsController::class, 'checkin'])
        ->name('dermaga.checkin');

    Route::get('/dermaga/boarding', [OperationsController::class, 'boarding'])
        ->name('dermaga.boarding');

    Route::get('/dermaga/dispatch', [OperationsController::class, 'dispatch'])
        ->name('dermaga.dispatch');

    Route::get('/dermaga/manifest', [OperationsController::class, 'manifest'])
        ->name('dermaga.manifest');

    Route::get('/operasional/pooling', [OperationsController::class, 'pooling'])
        ->name('operasional.pooling');

    Route::get('/operasional/antrian-boat', [OperationsController::class, 'queue'])
        ->name('operasional.queue');

    Route::get('/operasional/exception', [OperationsController::class, 'exception'])
        ->name('operasional.exception');

    Route::post('/operasional/pooling/{queue}/boat', [OperationsController::class, 'assignBoat'])
        ->name('operasional.pooling.assign-boat');

    Route::post('/operasional/antrian-boat/{queue}/call', [OperationsController::class, 'callQueue'])
        ->name('operasional.queue.call');

    Route::post('/dermaga/boarding/{queue}/start', [OperationsController::class, 'startBoarding'])
        ->name('dermaga.boarding.start');

    Route::post('/dermaga/dispatch/{queue}', [OperationsController::class, 'dispatchQueue'])
        ->name('dermaga.dispatch.queue');
});


/*
|--------------------------------------------------------------------------
| Master Data
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:superadmin,kasir,agen'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {

        /*
        |------------------------------------------------------------------
        | Boat
        |------------------------------------------------------------------
        */

        Route::get('/boats', [BoatController::class, 'index'])
            ->name('boats.index');

        Route::post('/boats', [BoatController::class, 'store'])
            ->name('boats.store');

        Route::put('/boats/{boat}', [BoatController::class, 'update'])
            ->name('boats.update');

        Route::post('/boats/{boat}/activate', [BoatController::class, 'activate'])
            ->name('boats.activate');

        Route::post('/boats/{boat}/deactivate', [BoatController::class, 'deactivate'])
            ->name('boats.deactivate');


        /*
        |------------------------------------------------------------------
        | Customer
        |------------------------------------------------------------------
        */

        Route::get('/customers', [CustomerController::class, 'index'])
            ->name('customers.index');

        Route::post('/customers', [CustomerController::class, 'store'])
            ->name('customers.store');

        Route::put('/customers/{customer}', [CustomerController::class, 'update'])
            ->name('customers.update');

        Route::post('/customers/{customer}/activate', [CustomerController::class, 'activate'])
            ->name('customers.activate');

        Route::post('/customers/{customer}/deactivate', [CustomerController::class, 'deactivate'])
            ->name('customers.deactivate');


        /*
        |------------------------------------------------------------------
        | Agent
        |------------------------------------------------------------------
        */

        Route::get('/agents', [AgentController::class, 'index'])
            ->name('agents.index');

        Route::post('/agents', [AgentController::class, 'store'])
            ->name('agents.store');

        Route::put('/agents/{agent}', [AgentController::class, 'update'])
            ->name('agents.update');

        Route::patch('/agents/{agent}/activate', [AgentController::class, 'activate'])
            ->name('agents.activate');

        Route::patch('/agents/{agent}/deactivate', [AgentController::class, 'deactivate'])
            ->name('agents.deactivate');


        /*
        |------------------------------------------------------------------
        | Trip / Jadwal
        |------------------------------------------------------------------
        */

        Route::get('/schedules', [OperationsController::class, 'tripSchedule'])
            ->name('schedules.index');

        /*
        |--------------------------------------------------------------------------
        | Tarif / Services
        |--------------------------------------------------------------------------
        */

        Route::get('/services', [OperationsController::class, 'tariff'])
            ->name('services.index');
    });


/*
|--------------------------------------------------------------------------
| Pooling
|--------------------------------------------------------------------------
|
| PoolingController lokal dipertahankan karena merupakan implementasi
| pooling berbasis Boat yang ada pada commit lokal.
|
*/

Route::prefix('pooling')
    ->name('pooling.')
    ->group(function () {

        Route::get('/', [PoolingController::class, 'index'])
            ->name('index');

        Route::post('/assign', [PoolingController::class, 'assign'])
            ->name('assign');
    });
