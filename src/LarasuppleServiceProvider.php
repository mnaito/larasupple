<?php

namespace Mits430\Larasupple;

use Illuminate\Support\ServiceProvider;

class LarasuppleServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__.'/routes/web.php';
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->make('Mits430\Larasupple\TestController');
    }
}
