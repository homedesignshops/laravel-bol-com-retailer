<?php

namespace HomeDesignShops\LaravelBolComRetailer\Tests;

use HomeDesignShops\LaravelBolComRetailer\BolComRetailerClient;
use HomeDesignShops\LaravelBolComRetailer\BolComRetailerService;
use HomeDesignShops\LaravelBolComRetailer\BolConfig;
use HomeDesignShops\LaravelBolComRetailer\BolService;
use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Picqer\BolRetailerV10\Client;
use Picqer\BolRetailerV10\Model\Order;
use Picqer\BolRetailerV10\Model\OrderItem;
use Picqer\BolRetailerV10\Model\ProcessStatus;

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

        $this->assertCount(1, $bolService->retailers());
    }

    /**
     * @test
     * TODO: Fake the Order
     */
    public function it_find_an_order()
    {
        $bolService = new BolService();

        $bolService->loadFromConfig(new BolConfig());

        $order = $bolService->findOrder('1043946570', array_key_first($bolService->retailers()));

        $this->assertInstanceOf(Order::class, $order);
    }

    /**
     * @test
     */
    public function it_ships_an_order()
    {
        $bolService = new BolService();

        $bolService->loadFromConfig(new BolConfig());

        $order = $bolService->findOrder('1043946570', array_key_first($bolService->retailers()));

        $transport = new Transport('TRUNKRS', '421401239', '12345678');

        $orderItem = new OrderItem();
        $orderItem->orderItemId = $order->orderItems[0]->orderItemId;
        $orderItem->quantity = $order->orderItems[0]->quantity;

        $processStatus = $bolService->retailers()['BOL_NL']->shipOrderItem($orderItem, $transport);

        $this->assertInstanceOf(ProcessStatus::class, $processStatus);
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_order_not_found()
    {
        $this->expectException(\Exception::class);

        $bolService = new BolService();

        $bolService->loadFromConfig(new BolConfig());

        $bolService->findOrder('1234567890abcdef', array_key_first($bolService->retailers()));
    }

    /** @test */
    public function it_find_open_orders()
    {
        // Given
        $bolService = new BolService();

        $bolService->loadFromConfig(new BolConfig());

        // When
        $orders = $bolService->openOrders();

        // Then
        $this->assertIsArray($orders);
        foreach ($orders as $order) {
            $this->assertInstanceOf(\HomeDesignShops\LaravelBolComRetailer\Models\Order::class, $order);
        }
    }
}
