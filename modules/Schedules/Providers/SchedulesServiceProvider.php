<?php

namespace Modules\Schedules\Providers;

use App\Http\Controllers\Frontend\FlightController as CoreFlightController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Schedules\Http\Controllers\Frontend\SchedulesController;

class SchedulesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../Config/config.php', 'schedules');

        // Routes resolve controllers through the container, so binding the core class to our
        // subclass swaps only index() for /flights. Nothing in core is edited, and every other
        // inherited action (search, show, bids) keeps working. Disable the module to undo it.
        if (config('schedules.override_flights_index', true)) {
            $this->app->bind(CoreFlightController::class, SchedulesController::class);
        }
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'schedules');
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

        Route::group([
            'middleware' => ['web'],
        ], function () {
            $this->loadRoutesFrom(__DIR__.'/../Http/Routes/web.php');
        });
    }
}
