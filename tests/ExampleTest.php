<?php

namespace Kevinkoenen\LaravelBolComRetailer\Tests;

use Orchestra\Testbench\TestCase;
use Kevinkoenen\LaravelBolComRetailer\LaravelBolComRetailerServiceProvider;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [LaravelBolComRetailerServiceProvider::class];
    }
    
    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
