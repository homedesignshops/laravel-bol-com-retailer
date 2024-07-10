<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use HomeDesignShops\LaravelBolComRetailer\Facades\Bol;
use Illuminate\Support\ServiceProvider;
use Picqer\BolRetailerV8\Client;

class BolComRetailerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'laravel-bol-com-retailer');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-bol-com-retailer');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('bol.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/laravel-bol-com-retailer'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/laravel-bol-com-retailer'),
            ], 'assets');*/

            // Publishing the translation files.
            /*$this->publishes([
                __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-bol-com-retailer'),
            ], 'lang');*/

            // Registering package commands.
            // $this->commands([]);
        }
    }

    /**
     * Register the application services.
     */
    public function register(): void
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'bol');

        $this->app->singleton(Bol::class, function() {
            $bolService = new BolService();

            $bolService->loadFromConfig(new BolConfig());

            return $bolService;
        });
    }
}
