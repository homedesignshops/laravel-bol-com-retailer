<?php

namespace HomeDesignShops\LaravelBolComRetailer\Facades;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Picqer\BolRetailer\Model\OrderItem;
use Picqer\BolRetailer\Order;
use Picqer\BolRetailer\ProcessStatus;

/**
 * @see \HomeDesignShops\LaravelBolComRetailer\BolComRetailerService
 *
 * @method static Collection getOpenOrders()
 * @method static ProcessStatus shipOrderItem(OrderItem $orderItem, Transport $transport)
 * @method static Order|null getOrder(string $orderId)
 */
class BolComRetailer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'bol-com-retailer';
    }
}
