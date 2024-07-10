<?php

namespace HomeDesignShops\LaravelBolComRetailer\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        // make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        $this->setupConfig($app);
        parent::getEnvironmentSetUp($app);
    }

    protected function setupConfig(Application $app): void
    {
        $app['config']->set('bol', include(__DIR__ . '/../config/config.php'));
    }
}
