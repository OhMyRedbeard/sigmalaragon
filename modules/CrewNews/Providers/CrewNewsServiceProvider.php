<?php

namespace Modules\CrewNews\Providers;

use App\Contracts\Modules\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Modules\CrewNews\Http\Controllers\NewsController;

class CrewNewsServiceProvider extends ServiceProvider
{
    private $moduleSvc;

    protected $defer = false;

    public function boot(): void
    {
        $this->moduleSvc = app('App\Services\ModuleService');

        $this->registerRoutes();
        $this->registerViews();
        $this->registerLinks();
    }

    public function register(): void
    {
        //
    }

    /**
     * Add the "News" link to the frontend navigation.
     * Set the last argument to true to show it only to logged-in users.
     */
    public function registerLinks(): void
    {
        $this->moduleSvc->addFrontendLink('News', '/news', '', false);
    }

    public function registerRoutes(): void
    {
        Route::middleware('web')
            ->prefix('news')
            ->name('crewnews.')
            ->group(function () {
                Route::get('/', [NewsController::class, 'index'])->name('index');
                Route::get('/{id}', [NewsController::class, 'show'])
                    ->whereNumber('id')
                    ->name('show');
            });
    }

    public function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'crewnews');
    }
}
