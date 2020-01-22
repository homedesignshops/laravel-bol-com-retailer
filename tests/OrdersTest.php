<?php

namespace HomeDesignShops\LaravelBolComRetailer\Tests;

use HomeDesignShops\LaravelBolComRetailer\Facades\BolComRetailer;
use HomeDesignShops\LaravelBolComRetailer\BolComRetailerServiceProvider;
use Illuminate\Support\Collection;
use Picqer\BolRetailer\Model\Order;

class OrdersTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [BolComRetailerServiceProvider::class];
    }

    /** @test */
    public function it_should_get_orders_with_customer_information()
    {
        // Given the order from the api
        /** @var Collection $orders */
        $orders = BolComRetailer::getOpenOrders();

        /** @var Order $order */
        $order = $orders->first();

        // Assert that the orders contains an orderId, orderItems and customerDetails
        $this->assertNotNull($order->orderId);
        $this->assertNotNull($order->orderItems);
        $this->assertTrue(count($order->orderItems) > 0);
        $this->assertNotNull($order->customerDetails);
        $this->assertNotNull($order->customerDetails->billingDetails);
        $this->assertNotNull($order->customerDetails->shipmentDetails);
    }
}
