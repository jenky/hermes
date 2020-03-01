<?php

namespace Jenky\Hermes;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Jenky\Hermes\Contracts\Hermes;

class HermesServiceProvider extends ServiceProvider implements DeferrableProvider
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
            __DIR__.'/../config/hermes.php', 'hermes'
        );

        $this->app->singleton(Hermes::class, function ($app) {
            return new GuzzleManager($app);
        });

        $this->app->alias(Hermes::class, 'hermes');
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
                __DIR__.'/../config/hermes.php' => config_path('hermes.php'),
            ], 'hermes-config');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            Hermes::class,
        ];
    }
}
