<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Picqer\BolRetailer\Client;
use Picqer\BolRetailer\Exception\RateLimitException;
use Picqer\BolRetailer\Model\Order;
use Picqer\BolRetailer\Model\OrderOrderItem;
use Picqer\BolRetailer\Model\ProcessStatus;
use Picqer\BolRetailer\Model\ReducedOrder;
use Picqer\BolRetailer\Model\ShipmentRequest;
use Picqer\BolRetailer\Model\ShipmentTransport;

class BolComRetailerClient
{
    /**
     * @var Client
     */
    protected Client $client;

    /**
     * Max retry counts for the API requests.
     *
     * @var int
     */
    protected int $maxRetries;

    /**
     * Holds the retry counts.
     * @var int
     */
    protected int $retriesCount = 0;

    /**
     * BolComRetailerClient constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
        $this->maxRetries = config('bol-com-retailer.max_retries');
    }

    /**
     * Returns a collection of the open orders.
     *
     * @return Collection|null
     * @throws RateLimitException
     */
    public function getOpenOrders(): ?Collection
    {
        try {
            return collect($this->client->getOrders()) // Client::getOrders()
                ->transform(function (ReducedOrder $reducedOrder) {
                    return $this->client->getOrder($reducedOrder->orderId);
                });

        } catch (RateLimitException $e) {
            $this->retriesCount++;
            $retryInSeconds = str_replace(['Too many requests, retry in ', ' seconds.'], '', $e->getMessage());

            sleep( (int) $retryInSeconds );

            if($this->retriesCount <= $this->maxRetries) {
                return $this->getOpenOrders();
            }

            throw $e;
        } catch (\Exception $e) {
            report($e);
            return null;
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
            return $this->client->getOrder($orderId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Ships an order item
     *
     * @param OrderOrderItem $orderItem
     * @param Transport $transport
     * @return ProcessStatus|null
     * @throws RateLimitException
     */
    public function shipOrderItem(OrderOrderItem $orderItem, Transport $transport): ?ProcessStatus
    {
        $shipmentRequest = new ShipmentRequest();
        $shipmentRequest->addOrderItemId($orderItem->orderItemId);

        $shipmentTransport = new ShipmentTransport();
        $shipmentTransport->transporterCode = $transport->transporterCode;
        $shipmentTransport->trackAndTrace = $transport->trackAndTraceCode;

        $shipmentRequest->transport = $shipmentTransport;

        try {
            return $this->client->shipOrderItem($shipmentRequest);

        } catch (RateLimitException $e) {
            $this->retriesCount++;
            $retryInSeconds = str_replace(['Too many requests, retry in ', ' seconds.'], '', $e->getMessage());

            sleep( (int) $retryInSeconds );

            if($this->retriesCount <= $this->maxRetries) {
                return $this->shipOrderItem($orderItem, $transport);
            }

            throw $e;
        } catch (\Exception $e) {
            report($e);

            return null;
        }
    }
}
