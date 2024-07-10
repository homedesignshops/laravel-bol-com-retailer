<?php

namespace HomeDesignShops\LaravelBolComRetailer;

class BolConfig
{
    /**
     * Returns the retailers from the config file.
     *
     * @return array
     */
    public static function loadRetailersFromConfig(): array
    {
        $retailerCodes = env('BOL_COM_CODES', '');

        return collect(explode(',' , $retailerCodes))
            ->map(function ($countryCode) {
                return [
                    'code' => $countryCode,
                    'use_demo_mode' => env('BOL_COM_USE_DEMO_MODE_' . $countryCode, true),
                    'client_id' => env('BOL_COM_CLIENT_ID_' . $countryCode),
                    'client_secret' => env('BOL_COM_CLIENT_SECRET_' . $countryCode),
                    'max_retries' => env('BOL_COM_MAX_RETRIES_' . $countryCode, env('BOL_COM_MAX_RETRIES', 5)),
                ];
            })->toArray();
    }
}
