<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use Picqer\BolRetailerV8\Client;

class BolService
{
    /**
     * @var BolComRetailerService[] $retailers
     */
    protected array $retailers = [];

    /**
     * Return all retailers.
     * @return BolComRetailerService[]
     */
    public function retailers(): array
    {
        return $this->retailers;
    }

    /**
     * Find a retailer by code.
     * @param string $code
     * @return BolComRetailerService
     */
    public function retailer(string $code): BolComRetailerService
    {
        return $this->retailers[$code];
    }

    /**
     * @param string $code
     * @param BolComRetailerService $bolComRetailerService
     * @return void
     */
    public function addRetailer(string $code, BolComRetailerService $bolComRetailerService): void
    {
        $this->retailers[$code] = $bolComRetailerService;
    }

    /**
     * @param array $config
     * @return void
     */
    public function loadFromConfig(array $config): void
    {
        collect($config['retailers'])->filter()->each(
            function (array $retailerConfig, string $retailerName) use ($config) {
                $bolComRetailerClient = new BolComRetailerClient(new Client(), $config['max_retries']);

                $retailer = new BolComRetailerService($bolComRetailerClient);
                $retailer->setCredentials($retailerConfig['client_id'], $retailerConfig['client_secret']);
                $retailer->setDemoMode($retailerConfig['use_demo_mode']);

                $this->addRetailer($retailerName, $retailer);
            });
    }
}
