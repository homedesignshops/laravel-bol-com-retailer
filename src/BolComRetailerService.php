<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use HomeDesignShops\LaravelBolComRetailer\Models\TransportData;
use HomeDesignShops\LaravelBolComRetailer\Models\TransportItem;
use Picqer\BolRetailer\Client as BolRetailerClient;

class BolComRetailerService
{
    /**
     * @var BolComRetailerClient
     */
    protected $client;

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

        $this->client = new BolComRetailerClient();
    }

    /**
     * Reauthenticate if needed.
     */
    public function reauthenticateIfNeeded(): void
    {
        if(BolRetailerClient::isAuthenticated() === false) {
            BolRetailerClient::setCredentials(
                config('bol-com-retailer.client_id'),
                config('bol-com-retailer.client_secret')
            );
        }
    }

    /**
     * Redirect all calls to the client
     *
     * @param string $name Name of the method to call
     * @param mixed $arguments Arguments of the method to call
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $this->reauthenticateIfNeeded();

        return $this->client->$name(...$arguments);
    }

}
