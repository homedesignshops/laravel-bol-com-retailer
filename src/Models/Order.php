<?php

namespace HomeDesignShops\LaravelBolComRetailer\Models;

class Order extends \Picqer\BolRetailerV10\Model\Order
{
    public string $retailerCode;

    public function __construct(string $retailerCode, array $attributes = [])
    {
        parent::fromArray($attributes);
        $this->retailerCode = $retailerCode;
    }
}
