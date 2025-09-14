<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Envato API Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration is used by the EnvatoLicenseValidator to validate
    | CodeCanyon purchase codes against Envato's API.
    |
    | To get your API token:
    | 1. Go to https://build.envato.com/api/#token
    | 2. Create a new token with "View and search Envato sites" permission
    | 3. Add the token to your .env file as ENVATO_API_TOKEN
    |
    | To find your item ID:
    | 1. Go to your CodeCanyon item page
    | 2. The item ID is in the URL: codecanyon.net/item/name/ITEM_ID
    |
    */

    'envato' => [
        // Your Envato API Personal Token
        'api_token' => env('ENVATO_API_TOKEN'),

        // Your CodeCanyon Item ID
        'item_id' => env('ENVATO_ITEM_ID'),

        // Development mode bypasses all Envato checks
        'dev_mode' => env('ENVATO_DEV_MODE', false),

        // API settings
        'timeout' => env('ENVATO_API_TIMEOUT', 30),
        'cache_duration' => env('ENVATO_CACHE_DURATION', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Other License Services
    |--------------------------------------------------------------------------
    |
    | Configuration for other license validation services
    |
    */

    // Example: Custom license server
    'custom_server' => [
        'url' => env('CUSTOM_LICENSE_SERVER'),
        'api_key' => env('CUSTOM_LICENSE_API_KEY'),
        'timeout' => env('CUSTOM_LICENSE_TIMEOUT', 30),
    ],

    // Example: Gumroad
    'gumroad' => [
        'product_id' => env('GUMROAD_PRODUCT_ID'),
        'api_key' => env('GUMROAD_API_KEY'),
    ],

    // Example: Paddle
    'paddle' => [
        'vendor_id' => env('PADDLE_VENDOR_ID'),
        'api_key' => env('PADDLE_API_KEY'),
        'product_id' => env('PADDLE_PRODUCT_ID'),
    ],
];
