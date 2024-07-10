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
    public Client $bolClient;

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
    public function __construct(Client $client, int $maxRetries = 5)
    {
        $this->bolClient = $client;
        $this->maxRetries = $maxRetries;
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
            return collect($this->bolClient->getOrders()) // Client::getOrders()
                ->transform(function (ReducedOrder $reducedOrder) {
                    return $this->bolClient->getOrder($reducedOrder->orderId);
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
            return $this->bolClient->getOrder($orderId);
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
            return $this->bolClient->shipOrderItem($shipmentRequest);

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
            return $this->bolClient->getOffer($offerId);
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
            return $this->bolClient->putOffer(
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
            return $this->bolClient->updateOfferStock(
                $offer->offerId,
                $updateOfferRequest
            );
        } catch (\Exception $exception) {
            report($exception);
            return null;
        }
    }

    /**
     * @throws ResponseException
     * @throws UnauthorizedException
     * @throws Exception
     * @throws ConnectException
     * @throws RateLimitException
     */
    public function authenticateByClientCredentials(string $clientId, string $clientSecret): void
    {
        $this->bolClient->authenticateByClientCredentials($clientId, $clientSecret);
    }

    /**
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return $this->bolClient->isAuthenticated();
    }

    public function setDemoMode(bool $enabled): void
    {
        $this->bolClient->setDemoMode($enabled);
    }
}
