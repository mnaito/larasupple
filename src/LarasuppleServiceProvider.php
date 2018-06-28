<?php

namespace Mits430\Larasupple;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;

class LarasuppleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        //include __DIR__ . '/routes/web.php';
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mapRoutes($this->app->router);

        //
        //$this->app->make('Mits430\Larasupple\TestController');
        //$this->app->make('Mits430\Larasupple\DebugController');
    }

    protected function mapRoutes(Router $router)
    {
        // all routing written within this package should be as belonged package
        $router
            ->namespace('\Mits430\Larasupple')
            ->group(function ($router) {
                require(__DIR__ . '/routes/web.php');
        });
    }
}
