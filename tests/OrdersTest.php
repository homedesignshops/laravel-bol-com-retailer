<?php

namespace HomeDesignShops\LaravelBolComRetailer\Tests;

use HomeDesignShops\LaravelBolComRetailer\Facades\BolComRetailer;
use HomeDesignShops\LaravelBolComRetailer\BolComRetailerServiceProvider;
use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
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

    /** @test */
    public function it_should_get_an_order()
    {
        // Given the order from the api
        /** @var Collection $orders */
        $order = BolComRetailer::getOrder('1043946570');

        $this->assertInstanceOf(\Picqer\BolRetailer\Order::class, $order);

        // Should return null if order not found
        $order = BolComRetailer::getOrder('123');
        $this->assertNull($order);
    }

    /** @test */
    public function it_should_ship_an_order()
    {
        // Given the order from the api
        /** @var Collection $orders */
        $order = BolComRetailer::getOrder('1043946570');

        // Grep the orderItem
        $orderItem = $order->orderItems[0];

        // Create a new Transport
        $transport = new Transport('TNT', '3SYUDM001092931', '120304514');

        // When we ship the orderItem with the given transport
        $processStatus = BolComRetailer::shipOrderItem($orderItem, $transport);

        // Assert it is pending
        $this->assertTrue($processStatus->isPending);

        // And the event type is CONFIRM_SHIPMENT
        $this->assertSame('CONFIRM_SHIPMENT', $processStatus->eventType);
    }
}
