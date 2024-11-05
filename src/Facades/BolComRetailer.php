<?php

namespace HomeDesignShops\LaravelBolComRetailer\Facades;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Facade;
use Picqer\BolRetailerV10\Model\Order;
use Picqer\BolRetailerV10\Model\OrderOrderItem;
use Picqer\BolRetailerV10\Model\ProcessStatus;
use Picqer\BolRetailerV10\Model\RetailerOffer;

/**
 * @see \HomeDesignShops\LaravelBolComRetailer\BolComRetailerService
 *
 * @method static Collection getOpenOrders()
 * @method static ProcessStatus shipOrderItem(OrderOrderItem $orderItem, Transport $transport)
 * @method static Order|null getOrder(string $orderId)
 * @method static RetailerOffer|null getOffer(string $offerId)
 * @method static ProcessStatus|null updateOffer(RetailerOffer $offer)
 * @method static ProcessStatus|null updateOfferStock(RetailerOffer $offer, int $stock, bool $managedByRetailer = false)
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
