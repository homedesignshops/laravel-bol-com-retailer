<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use Picqer\BolRetailerV8\Client;
use Picqer\BolRetailerV8\Model\Order;

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
     * @param BolConfig $config
     * @return void
     */
    public function loadFromConfig(BolConfig $config): void
    {
        collect($config::loadRetailersFromConfig())->filter()->each(
            function (array $retailerConfig, string $retailerName) {
                $bolComRetailerClient = new BolComRetailerClient(new Client(), $retailerConfig['max_retries']);

                $retailer = new BolComRetailerService($bolComRetailerClient);
                $retailer->setCredentials($retailerConfig['client_id'], $retailerConfig['client_secret']);
                $retailer->setDemoMode($retailerConfig['use_demo_mode']);

                $this->addRetailer($retailerName, $retailer);
            });
    }

    /**
     * @throws \Throwable
     */
    public function findOrder(string $orderId, string $code = null): Order
    {
        $retailers = $code ? [$this->retailer($code)] : $this->retailers;

        $order = null;
        foreach ($retailers as $retailer) {
            $order = $retailer->getOrder($orderId);
            if (!empty($order)) {
                break;
            }
        }

        throw_unless($order, new \Exception("Order $orderId not found"));

        return $order;
    }

    /**
     * @return Order[]
     */
    public function openOrders(): array
    {
        $orders = [];
        foreach ($this->retailers as $retailer) {
            $orders = array_merge($orders, $retailer->getOpenOrders()->toArray());
        }

        return $orders;
    }
}
