<?php

// config for Vanadi/Framework
use Vanadi\Framework\Models\Country;
use Vanadi\Framework\Models\Permission;
use Vanadi\Framework\Models\Role;
use Vanadi\Framework\Models\Team;
use Vanadi\Framework\Models\User;

return [
    'shared_models' => [
        'App\Models\User',
        User::class,
        Role::class,
        Permission::class,
        Country::class,
        \Vanadi\Framework\Models\Currency::class,
        Team::class,
    ],
    'currency' => [
        'exchange_rate_endpoint' => env('EXCHANGE_RATES_ENDPOINT', 'https://api.exchangeratesapi.io/live'),
        'exchange_rates_api_key' => env('EXCHANGE_RATES_API_KEY'),
    ]
];
