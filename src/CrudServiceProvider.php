<?php

namespace BalajiDharma\LaravelCrud;

use Illuminate\Support\ServiceProvider;

class CrudServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/crud.php', 'crud'
        );
    }

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'crud');

        if (app()->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/crud.php' => config_path('crud.php'),
            ], ['config', 'crud-config', 'admin-core', 'admin-core-config']);
        }
    }
}
