<?php

namespace HomeDesignShops\LaravelBolComRetailer\Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        // make sure, our .env file is loaded
        $app->useEnvironmentPath(__DIR__ . '/..');
        $app->bootstrapWith([LoadEnvironmentVariables::class]);
        $this->setupConfig($app);
        parent::getEnvironmentSetUp($app);
    }

    protected function setupConfig(Application $app)
    {
        $app['config']->set('bol-com-retailer.client_id', getenv('BOL_COM_CLIENT_ID'));
        $app['config']->set('bol-com-retailer.client_secret', getenv('BOL_COM_CLIENT_SECRET'));
    }
}
