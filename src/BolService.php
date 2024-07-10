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
            function (array $retailerConfig) {
                $bolComRetailerClient = new BolComRetailerClient(new Client(), $retailerConfig['max_retries']);

                $retailer = new BolComRetailerService($bolComRetailerClient);
                $retailer->setCredentials($retailerConfig['client_id'], $retailerConfig['client_secret']);
                $retailer->setDemoMode($retailerConfig['use_demo_mode']);

                $this->addRetailer($retailerConfig['code'], $retailer);
            });
    }

    /**
     * Bij het verzenden van een order weten we niet bij welke retailer de order hoort.
     * Daarom kan de retailer code optioneel worden meegegeven.
     * @throws \Throwable
     */
    public function findOrder(string $orderId, string $retailerCode = null): \HomeDesignShops\LaravelBolComRetailer\Models\Order
    {
        $bolOrder = $retailerCode ?
            $this->findOrderForRetailer($orderId, $retailerCode) :
            $this->findOrderForAllRetailers($orderId);

        throw_if(empty($bolOrder), new \Exception("Order {$orderId} not found"));

        return new \HomeDesignShops\LaravelBolComRetailer\Models\Order($retailerCode, $bolOrder->toArray(false));
    }

    protected function findOrderForRetailer(string $orderId, string $retailerCode): ?Order
    {
        return $this->retailer($retailerCode)->getOrder($orderId);
    }

    protected function findOrderForAllRetailers(string $orderId): ?Order
    {
        $order = null;
        foreach ($this->retailers as $code => $retailer) {
            $order = $retailer->getOrder($orderId);
            if (empty($order)) {
                continue;
            }
        }

        return $order;
    }

    /**
     * @return \HomeDesignShops\LaravelBolComRetailer\Models\Order[]
     */
    public function openOrders(): array
    {
        $orders = [];

        foreach ($this->retailers as $code => $retailer) {
            $orders = array_merge(
                $orders,
                $retailer->getOpenOrders()
                    ->transform(fn(Order $order) => new \HomeDesignShops\LaravelBolComRetailer\Models\Order($code, $order->toArray(false)))
                    ->toArray()
            );
        }

        return $orders;
    }
}
