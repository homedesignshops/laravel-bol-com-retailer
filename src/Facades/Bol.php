<?php

namespace HomeDesignShops\LaravelBolComRetailer\Facades;

use HomeDesignShops\LaravelBolComRetailer\BolComRetailerService;
use HomeDesignShops\LaravelBolComRetailer\BolService;
use Illuminate\Support\Facades\Facade;


/**
 * @method BolComRetailerService[] retailers()
 * @method BolComRetailerService retailer(string $code)
 */
class Bol extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BolService::class;
    }
}
