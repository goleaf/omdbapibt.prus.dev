<?php

return [
    'accounts' => [
        'admin' => [
            'name' => env('SEED_ADMIN_NAME', 'Demo Administrator'),
            'email' => env('SEED_ADMIN_EMAIL', 'admin@example.com'),
            'password' => env('SEED_ADMIN_PASSWORD', 'password'),
            'preferred_locale' => env('SEED_ADMIN_LOCALE', 'en'),
        ],
        'demo' => [
            'name' => env('SEED_DEMO_NAME', 'Demo Subscriber'),
            'email' => env('SEED_DEMO_EMAIL', 'demo@example.com'),
            'password' => env('SEED_DEMO_PASSWORD', 'password'),
            'preferred_locale' => env('SEED_DEMO_LOCALE', 'en'),
        ],
    ],
];
