<?php

namespace HomeDesignShops\LaravelBolComRetailer\Facades;

use HomeDesignShops\LaravelBolComRetailer\BolComRetailerService;
use HomeDesignShops\LaravelBolComRetailer\BolService;
use Illuminate\Support\Facades\Facade;
use Picqer\BolRetailerV8\Model\Order;


/**
 * @method static BolComRetailerService[] retailers()
 * @method static BolComRetailerService retailer(string $code)
 * @method static Order[] openOrders()
 * @method static Order findOrder(string $orderId, string $code = null)
 */
class Bol extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BolService::class;
    }
}
