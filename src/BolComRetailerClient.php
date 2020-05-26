<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Picqer\BolRetailer\Exception\HttpException;
use Picqer\BolRetailer\Model\OrderItem;
use Picqer\BolRetailer\Model\ReducedOrder;
use Picqer\BolRetailer\Order;
use Picqer\BolRetailer\ProcessStatus;
use Picqer\BolRetailer\Shipment;

class BolComRetailerClient
{
    /**
     * Max retry counts for the API requests.
     *
     * @var int
     */
    protected $maxRetries;

    /**
     * Holds the retry counts.
     * @var int
     */
    protected $retriesCount = 0;

    /**
     * BolComRetailerClient constructor.
     */
    public function __construct()
    {
        $this->maxRetries = config('bol-com-retailer.max_retries');
    }

    /**
     * Returns a collection of the open orders.
     *
     * @return Collection
     */
    public function getOpenOrders(): Collection
    {
        try {
            return collect(Order::all())
                ->transform(static function (ReducedOrder $reducedOrder) {
                    return Order::get($reducedOrder->orderId);
                });

        } catch (HttpException $e) {
            $this->retriesCount++;
            $retryInSeconds = str_replace(['Too many requests, retry in ', ' seconds.'], '', $e->getDetail());

            sleep( (int) $retryInSeconds );

            if($this->retriesCount <= $this->maxRetries) {
                return $this->getOpenOrders();
            }

            throw $e;
        }
    }

    /**
     * Returns a Bol.com order.
     * Null if order not found.
     *
     * @param string $orderId
     * @return Order|null
     */
    public function getOrder(string $orderId)
    {
        try {
            return Order::get($orderId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Ships a order item
     *
     * @param OrderItem $orderItem
     * @param Transport $transport
     * @return ProcessStatus
     */
    public function shipOrderItem(OrderItem $orderItem, Transport $transport): ProcessStatus
    {
        try {
            return Shipment::create($orderItem, [
                'shipmentReference' => $transport->shipmentReference,
                'transport' => [
                    'transporterCode' => $transport->transporterCode,
                    'trackAndTrace' => $transport->trackAndTraceCode
                ]
            ]);

        } catch (HttpException $e) {
            $this->retriesCount++;
            $retryInSeconds = str_replace(['Too many requests, retry in ', ' seconds.'], '', $e->getDetail());

            sleep( (int) $retryInSeconds );

            if($this->retriesCount <= $this->maxRetries) {
                return $this->shipOrderItem($orderItem, $transport);
            }

            throw $e;
        }
    }
}
