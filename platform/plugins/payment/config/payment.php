<?php

return [
    'currency' => env('PAYMENT_DEFAULT_CURRENCY', 'USD'),

    'providers' => [
        'omise' => [
            'enabled' => env('PAYMENT_OMISE_ENABLED', false),
            'public_key' => env('PAYMENT_OMISE_PUBLIC_KEY'),
            'script_url' => env('PAYMENT_OMISE_SCRIPT_URL', 'https://cdn.omise.co/omise.js'),
            'currency' => env('PAYMENT_OMISE_CURRENCY'),
        ],
    ],
];
