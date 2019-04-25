<?php

namespace Jenky\Guzzilla;

use Illuminate\Support\ServiceProvider;

class GuzzillaServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPublishing();
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/guzzilla.php', 'guzzilla'
        );
    }

    /**
     * Register the package's publishable resources.
     *
     * @return void
     */
    protected function registerPublishing()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/guzzilla.php' => config_path('guzzilla.php'),
            ], 'guzzilla-config');
        }
    }
}
