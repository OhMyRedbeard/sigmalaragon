<?php

use Illuminate\Support\Facades\Route;
use Modules\CrewNews\Http\Controllers\Admin\ArticleController;

Route::middleware(['web', 'auth', 'ability:admin,admin-access'])
    ->prefix('admin/crewnews')
    ->name('crewnews.admin.')
    ->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('index');
        Route::get('/create', [ArticleController::class, 'create'])->name('create');
        Route::post('/', [ArticleController::class, 'store'])->name('store');
        Route::get('/{article:id}/edit', [ArticleController::class, 'edit'])->name('edit');
        Route::put('/{article:id}', [ArticleController::class, 'update'])->name('update');
        Route::delete('/{article:id}', [ArticleController::class, 'destroy'])->name('destroy');
    });
