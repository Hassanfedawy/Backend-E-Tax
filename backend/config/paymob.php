<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Paymob API Configuration
    |--------------------------------------------------------------------------
    | These values are pulled from your .env file. Always use config() to access
    | them in your application to ensure they work after config caching.
    |
    */

    'api_key' => env('PAYMOB_API_KEY'),
    'integration_id' => env('PAYMOB_INTEGRATION_ID'),
    'iframe_id' => env('PAYMOB_IFRAME_ID'),
    'hmac' => env('PAYMOB_HMAC'),

];
