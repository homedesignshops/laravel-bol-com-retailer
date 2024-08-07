<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Picqer\BolRetailerV8\Client;
use Picqer\BolRetailerV8\Exception\ConnectException;
use Picqer\BolRetailerV8\Exception\Exception;
use Picqer\BolRetailerV8\Exception\RateLimitException;
use Picqer\BolRetailerV8\Exception\ResponseException;
use Picqer\BolRetailerV8\Exception\UnauthorizedException;
use Picqer\BolRetailerV8\Model\Order;
use Picqer\BolRetailerV8\Model\OrderOrderItem;
use Picqer\BolRetailerV8\Model\ProcessStatus;
use Picqer\BolRetailerV8\Model\ReducedOrder;
use Picqer\BolRetailerV8\Model\RetailerOffer;
use Picqer\BolRetailerV8\Model\ShipmentRequest;
use Picqer\BolRetailerV8\Model\ShipmentTransport;
use Picqer\BolRetailerV8\Model\UpdateOfferRequest;
use Picqer\BolRetailerV8\Model\UpdateOfferStockRequest;

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
    public function getOrder(string $orderId): ?Order
    {
        try {
            return $this->client->getOrder($orderId);
        } catch (\Exception $e) {
            report($e);
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
        $shipmentRequest->shipmentReference = $transport->shipmentReference;

        $shipmentTransport = new ShipmentTransport();
        $shipmentTransport->transporterCode = $transport->transporterCode;
        $shipmentTransport->trackAndTrace = $transport->trackAndTraceCode;

        $shipmentTransport->transportEvents = null;

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

    /**
     * @param string $offerId
     * @return RetailerOffer|null
     */
    public function getOffer(string $offerId): ?RetailerOffer
    {
        try {
            return $this->client->getOffer($offerId);
        } catch (\Exception $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * @param RetailerOffer $offer
     * @return ProcessStatus|null
     */
    public function updateOffer(RetailerOffer $offer): ?ProcessStatus
    {
        $updateOfferRequest = new UpdateOfferRequest();
        $updateOfferRequest->onHoldByRetailer = $offer->onHoldByRetailer;
        $updateOfferRequest->fulfilment = $offer->fulfilment;

        try {
            return $this->client->putOffer(
                $offer->offerId,
                $updateOfferRequest
            );
        } catch (\Exception $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * @param RetailerOffer $offer
     * @param int $stock
     * @return ProcessStatus|null
     */
    public function updateOfferStock(RetailerOffer $offer, int $stock, bool $managedByRetailer = false): ?ProcessStatus
    {
        $updateOfferRequest = new UpdateOfferStockRequest();
        $updateOfferRequest->amount = $stock;
        $updateOfferRequest->managedByRetailer = $managedByRetailer;

        try {
            return $this->client->updateOfferStock(
                $offer->offerId,
                $updateOfferRequest
            );
        } catch (\Exception $exception) {
            report($exception);
            return null;
        }
    }
}
