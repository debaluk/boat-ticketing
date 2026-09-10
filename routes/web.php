<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Master\BoatController;
use App\Http\Controllers\Master\CustomerController;
use App\Http\Controllers\Master\AgentController;
use App\Http\Controllers\Ticketing\TicketingController;


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

    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return redirect()->route('login');

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

//transaksi
Route::middleware(['auth', 'role:superadmin,kasir,agen'])->group(function () {

    Route::get('/ticketing', [TicketingController::class, 'index'])
        ->name('ticketing.index');

    Route::post('/ticketing', [TicketingController::class, 'store'])
        ->name('ticketing.store');

});

/*
|--------------------------------------------------------------------------
| MASTER DATA
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:superadmin,kasir,agen'])
    ->prefix('master')
    ->name('master.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | BOAT
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | CUSTOMER
        |--------------------------------------------------------------------------
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
        |--------------------------------------------------------------------------
        | AGENT
        |--------------------------------------------------------------------------
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

    });
/*
|--------------------------------------------------------------------------
| Trip / Jadwal
|--------------------------------------------------------------------------
*/
Route::prefix('trip')->name('trip.')->group(function () {
    Route::resource('schedules', \App\Http\Controllers\Trip\ScheduleController::class)
        ->except(['show']);
});

/*
|--------------------------------------------------------------------------
| Pooling
|--------------------------------------------------------------------------
*/
Route::prefix('pooling')->name('pooling.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Pooling\PoolingController::class, 'index'])
        ->name('index');

    Route::post('/assign', [\App\Http\Controllers\Pooling\PoolingController::class, 'assign'])
        ->name('assign');
});
