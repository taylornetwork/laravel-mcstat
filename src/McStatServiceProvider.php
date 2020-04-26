<?php


namespace TaylorNetwork\LaravelMcStat;

use Illuminate\Support\ServiceProvider;

class McStatServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/mcstat.php', 'mcstat');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/config/mcstat.php' => config_path(),
        ],'config');

        $this->publishes([
            __DIR__.'/migrations' => database_path('migrations'),
        ],'migrations');
    }
}