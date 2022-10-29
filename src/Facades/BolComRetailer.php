<?php

namespace HomeDesignShops\LaravelBolComRetailer\Facades;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Picqer\BolRetailerV8\Model\Order;
use Picqer\BolRetailerV8\Model\OrderOrderItem;
use Picqer\BolRetailerV8\Model\ProcessStatus;

/**
 * @see \HomeDesignShops\LaravelBolComRetailer\BolComRetailerService
 *
 * @method static Collection getOpenOrders()
 * @method static ProcessStatus shipOrderItem(OrderOrderItem $orderItem, Transport $transport)
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
