<?php

use Illuminate\Support\Facades\Route;
use Modules\FlightOperations\Http\Controllers\FlightOperationsController;

Route::middleware('auth')
    ->prefix('flightoperations')
    ->name('flightoperations.')
    ->group(function () {
        Route::get('/', [FlightOperationsController::class, 'index'])->name('index');
        Route::get('/airports', [FlightOperationsController::class, 'searchAirports'])
            ->middleware('throttle:120,1')
            ->name('airports');
        Route::post('/create-flight', [FlightOperationsController::class, 'createFlight'])->name('create-flight');
        Route::post('/delete-bid/{bidId}', [FlightOperationsController::class, 'deleteBid'])->name('delete-bid');
        Route::post('/delete-simbrief/{id}', [FlightOperationsController::class, 'deleteSimbrief'])->name('delete-simbrief');
    });
