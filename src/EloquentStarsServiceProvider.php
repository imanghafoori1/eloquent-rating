<?php

namespace Imanghafoori\Stars;

use Illuminate\Support\ServiceProvider;

class EloquentStarsServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/stars.php' => config_path('star.php'),
            ]);
        }

        $this->mergeConfigFrom(__DIR__.'/../config/stars.php', 'star');
        $this->loadMigrationsFrom(__DIR__.'/../migrations');
    }
}
