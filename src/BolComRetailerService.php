<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use Illuminate\Support\Collection;
use Picqer\BolRetailer\Client as BolRetailerClient;
use Picqer\BolRetailer\Model\ReducedOrder;
use Picqer\BolRetailer\Order;

class BolComRetailerService
{

    /**
     * BolComRetailer constructor.
     */
    public function __construct()
    {
        BolRetailerClient::setDemoMode(config('bol-com-retailer.use_demo_mode'));
        BolRetailerClient::setCredentials(
            config('bol-com-retailer.client_id'),
            config('bol-com-retailer.client_secret')
        );
    }

    /**
     * Returns a collection of the open orders.
     *
     * @return Collection
     */
    public function getOpenOrders(): Collection
    {
        return collect(Order::all())
            ->transform(static function (ReducedOrder $reducedOrder) {
                return Order::get($reducedOrder->orderId);
            });
    }

}
