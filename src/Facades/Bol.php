<?php

namespace HomeDesignShops\LaravelBolComRetailer\Facades;

use HomeDesignShops\LaravelBolComRetailer\BolComRetailerService;
use HomeDesignShops\LaravelBolComRetailer\BolService;
use Illuminate\Support\Facades\Facade;
use Picqer\BolRetailerV8\Model\Order;


/**
 * @method BolComRetailerService[] retailers()
 * @method BolComRetailerService retailer(string $code)
 * @method Order[] openOrders()
 * @method Order findOrder(string $orderId, string $code = null)
 */
class Bol extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BolService::class;
    }
}
