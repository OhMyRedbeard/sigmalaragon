<?php

namespace Modules\CrewNews\Providers;

use App\Contracts\Modules\ServiceProvider;

class CrewNewsServiceProvider extends ServiceProvider
{
    private $moduleSvc;

    protected $defer = false;

    public function boot(): void
    {
        $this->moduleSvc = app('App\Services\ModuleService');

        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/admin.php');
        $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');
        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'crewnews');

        $this->registerLinks();
    }

    public function register(): void
    {
        //
    }

    public function registerLinks(): void
    {
        // Public menu link (last argument: true = logged-in users only).
        $this->moduleSvc->addFrontendLink('News', '/news', '', false);

        // Admin sidebar link.
        $this->moduleSvc->addAdminLink('News', '/admin/crewnews', 'pe-7s-news-paper');
    }
}
