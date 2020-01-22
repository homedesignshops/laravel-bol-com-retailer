<?php

namespace HomeDesignShops\LaravelBolComRetailer;

use Illuminate\Support\Facades\Facade;

/**
 * @see \HomeDesignShops\LaravelBolComRetailer\Skeleton\SkeletonClass
 */
class LaravelBolComRetailerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-bol-com-retailer';
    }
}
