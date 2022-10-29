<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use Picqer\BolRetailerV8\Client;
use Picqer\BolRetailerV8\Client as BolRetailerClient;
use Picqer\BolRetailerV8\Exception\ConnectException;
use Picqer\BolRetailerV8\Exception\Exception;
use Picqer\BolRetailerV8\Exception\RateLimitException;
use Picqer\BolRetailerV8\Exception\ResponseException;
use Picqer\BolRetailerV8\Exception\UnauthorizedException;

class BolComRetailerService
{
    /**
     * @var BolRetailerClient $bolClient
     */
    protected Client $bolClient;

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
     * @throws ConnectException
     * @throws UnauthorizedException
     * @throws ResponseException
     * @throws RateLimitException
     * @throws \Exception
     */
    public function __construct($clientId, $clientSecret, $demoMode = true)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $this->bolClient = new Client();

        $this->bolClient->setDemoMode($demoMode === true);

        $this->authenticate();

        $this->client = new BolComRetailerClient($this->bolClient);
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
        $this->bolClient->authenticate($this->clientId, $this->clientSecret);
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
        if($this->bolClient->isAuthenticated() === false) {
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
