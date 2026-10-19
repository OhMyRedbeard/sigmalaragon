<?php

use Illuminate\Support\Facades\Route;
use Modules\Schedules\Http\Controllers\Frontend\SchedulesController;

// Same protection as the core /flights routes
$middleware = config('phpvms.registration.email_verification', false) ? ['auth', 'verified'] : ['auth'];

Route::group([
    'prefix'     => '',
    'as'         => 'frontend.schedules.',
    'middleware' => $middleware,
], function () {
    $uri = config('schedules.uri', 'schedules');

    Route::get($uri, [SchedulesController::class, 'index'])->name('index');
    Route::post($uri.'/bids', [SchedulesController::class, 'addBid'])->name('bid');
});
