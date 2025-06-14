<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use Illuminate\Support\ServiceProvider;
use Throwable;

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
                __DIR__.'/../config/config.php' => config_path('bol-com-retailer.php'),
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
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'bol-com-retailer');

        // Register the main class to use with the facade
        $this->app->singleton('bol-com-retailer', static function () {

            $config = config('bol-com-retailer');

            try {
                return new BolComRetailerService($config['client_id'], $config['client_secret'], $config['use_demo_mode']);
            } catch (Throwable $e) {
                report($e);
                return null;
            }
        });
    }
}
