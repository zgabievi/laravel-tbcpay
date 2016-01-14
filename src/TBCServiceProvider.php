<?php

namespace Gabievi\TBC;

use Illuminate\Support\ServiceProvider;

class TBCServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/config.php' => config_path('tbc.php'),
        ]);
    }

    /**
     * Register the application services.
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'tbc');

        $this->app['tbc'] = $this->app->share(function () {
            return new TBC();
        });
    }
}