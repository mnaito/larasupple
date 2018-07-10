<?php

namespace Mits430\Larasupple;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Mits430\Larasupple\Middleware\AutoViewselect;
use Mits430\Larasupple\Middleware\RequestLogger;
use Mits430\Larasupple\Middleware\ResponseLogger;

class LarasuppleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(Router $router)
    {
        $this->loadViewsFrom(__DIR__.'/resources/views', 'larasupple');
        
        $this->publishes([
            __DIR__.'/config/ytake-laravel-smarty.php' => config_path('ytake-laravel-smarty.php'),
            __DIR__.'/.env.example'                    => base_path(),
            __DIR__.'/public/vendor/larasupple'        => public_path('vendor/larasupple'),
        ]);

        //
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', AutoViewselect::class);
        $router->pushMiddlewareToGroup('web', RequestLogger::class);
        $router->pushMiddlewareToGroup('web', ResponseLogger::class);
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
            ->namespace('\Mits430\Larasupple\Controllers')
            ->group(function ($router) {
                require(__DIR__ . '/routes/web.php');
        });
    }
}
