<?php

use Illuminate\Support\Facades\Route;
use Modules\CrewNews\Http\Controllers\NewsController;

Route::middleware('web')
    ->prefix('news')
    ->name('crewnews.')
    ->group(function () {
        Route::get('/', [NewsController::class, 'index'])->name('index');
        Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
    });
