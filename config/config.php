<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Demo mode
    |--------------------------------------------------------------------------
    |
    | This value determines the service to run in demo mode.
    |
    */
    'use_demo_mode' => env('BOL_COM_USE_DEMO_MODE', true),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | The authentication uses a client id and client secret make use
    | of this service.
    |
    */
    'client_id' => env('BOL_COM_CLIENT_ID'),
    'client_secret' => env('BOL_COM_CLIENT_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Max retries
    |--------------------------------------------------------------------------
    |
    | The max retries for the Bol.com API.
    |
    */
    'max_retries' => env('BOL_COM_MAX_RETRIES', 5),

    /*
    |--------------------------------------------------------------------------
    | Transport codes Bol.com
    |--------------------------------------------------------------------------
    |
    | We use this for the mapping. Feel free to add extra codes to map.
    |
    */
    'transport_codes' => [
        'tnt' => [
            'postnl',
        ],
        'gls' => []
    ]

];
