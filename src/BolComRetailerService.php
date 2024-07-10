<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use HomeDesignShops\LaravelBolComRetailer\Models\Transport;
use Illuminate\Support\Collection;
use Picqer\BolRetailerV8\Exception\ConnectException;
use Picqer\BolRetailerV8\Exception\Exception;
use Picqer\BolRetailerV8\Exception\RateLimitException;
use Picqer\BolRetailerV8\Exception\ResponseException;
use Picqer\BolRetailerV8\Exception\UnauthorizedException;
use Picqer\BolRetailerV8\Model\Order;
use Picqer\BolRetailerV8\Model\OrderOrderItem;
use Picqer\BolRetailerV8\Model\ProcessStatus;
use Picqer\BolRetailerV8\Model\RetailerOffer;

/**
 * @method Collection getOpenOrders()
 * @method ProcessStatus shipOrderItem(OrderOrderItem $orderItem, Transport $transport)
 * @method Order|null getOrder(string $orderId)
 * @method RetailerOffer|null getOffer(string $offerId)
 * @method ProcessStatus|null updateOffer(RetailerOffer $offer)
 * @method ProcessStatus|null updateOfferStock(RetailerOffer $offer, int $stock, bool $managedByRetailer = false)
 */

class BolComRetailerService
{
    /**
     * @var BolComRetailerClient
     */
    protected BolComRetailerClient $client;

    /**
     * @var string $clientId The client id of the Bol.com API;
     */
    protected string $clientId;

    /**
     * @var string $clientSecret The client secret of the Bol.com API.
     */
    protected string $clientSecret;

    /**
     * BolComRetailer constructor.
     */
    public function __construct(BolComRetailerClient $client)
    {
        $this->client = $client;
    }

    public function setCredentials(string $clientId, string $clientSecret): void
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function setDemoMode(bool $enabled): void
    {
        $this->client->setDemoMode($enabled);
    }

    /**
     * Authenticate to the Bol.com API.
     *
     * @throws Exception
     * @throws ResponseException
     * @throws RateLimitException
     * @throws UnauthorizedException
     * @throws ConnectException
     */
    protected function authenticate(): void
    {
        $this->client->authenticateByClientCredentials($this->clientId, $this->clientSecret);
    }

    /**
     * Reauthenticate if needed.
     *
     * @throws Exception
     * @throws ResponseException
     * @throws RateLimitException
     * @throws UnauthorizedException
     * @throws ConnectException
     */
    public function reauthenticateIfNeeded(): void
    {
        if($this->client->isAuthenticated() === false) {
            $this->authenticate();
        }
    }

    /**
     * Redirect all calls to the client
     *
     * @param string $name Name of the method to call
     * @param mixed $arguments Arguments of the method to call
     * @return mixed
     */
    public function __call(string $name, mixed $arguments)
    {
        try {
            $this->reauthenticateIfNeeded();
        } catch (\Exception $e) {
            report($e);
        }

        return $this->client->$name(...$arguments);
    }

}
