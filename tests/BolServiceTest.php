<?php

namespace HomeDesignShops\LaravelBolComRetailer\Tests;

use HomeDesignShops\LaravelBolComRetailer\BolComRetailerClient;
use HomeDesignShops\LaravelBolComRetailer\BolComRetailerService;
use HomeDesignShops\LaravelBolComRetailer\BolConfig;
use HomeDesignShops\LaravelBolComRetailer\BolService;
use Picqer\BolRetailerV8\Client;

class BolServiceTest extends TestCase
{

    /** @test
     */
    public function it_handles_retailer()
    {
        $bolClient = \Mockery::mock(Client::class)->makePartial();
        $bolComRetailerClient = \Mockery::mock(BolComRetailerClient::class, [$bolClient])->makePartial();

        $bolComRetailerClient->shouldReceive('setDemoMode')->once()->with(true);

        $retailer = \Mockery::mock(BolComRetailerService::class, [$bolComRetailerClient])->makePartial();
        $retailer->setCredentials('test-client-id', 'test-client-secret');
        $retailer->setDemoMode(true);

        $bolService = new BolService();
        $bolService->addRetailer('test-retailer', $retailer);

        $this->assertCount(1, $bolService->retailers());
        $this->assertSame($retailer, $bolService->retailer('test-retailer'));
    }

    /**
     * @test
     */
    public function it_loads_from_a_config_file()
    {
        $bolService = new BolService();

        $bolService->loadFromConfig(new BolConfig());

        $this->assertCount(2, $bolService->retailers());
    }
}
