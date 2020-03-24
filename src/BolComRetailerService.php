<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use HomeDesignShops\LaravelBolComRetailer\Models\TransportData;
use HomeDesignShops\LaravelBolComRetailer\Models\TransportItem;
use Illuminate\Support\Collection;
use Picqer\BolRetailer\Client as BolRetailerClient;
use Picqer\BolRetailer\Exception\HttpException;
use Picqer\BolRetailer\Model\OrderItem;
use Picqer\BolRetailer\Model\ReducedOrder;
use Picqer\BolRetailer\Order;
use Picqer\BolRetailer\ProcessStatus;
use Picqer\BolRetailer\Shipment;

class BolComRetailerService
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
     * BolComRetailer constructor.
     */
    public function __construct()
    {
        BolRetailerClient::setDemoMode(config('bol-com-retailer.use_demo_mode'));
        BolRetailerClient::setCredentials(
            config('bol-com-retailer.client_id'),
            config('bol-com-retailer.client_secret')
        );

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
                'transport' => [
                    'transporterCode' => $transport->transporterCode,
                    'trackAndTrace' => $transport->trackingCode
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
