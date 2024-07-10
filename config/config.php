<?php

return [

    'retailers' => \HomeDesignShops\LaravelBolComRetailer\BolConfig::loadRetailersFromConfig(),

    /*
    |--------------------------------------------------------------------------
    | Max retries
    |--------------------------------------------------------------------------
    |
    | The max retries for the Bol.com API.
    |
    */
    'max_retries' => env('BOL_COM_MAX_RETRIES', 5),

];
