<?php

namespace Kevinkoenen\LaravelBolComRetailer;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kevinkoenen\LaravelBolComRetailer\Skeleton\SkeletonClass
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
