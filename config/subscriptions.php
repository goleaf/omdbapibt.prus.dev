<?php

return [
    'cache' => [
        'metrics_ttl' => (int) env('SUBSCRIPTION_ANALYTICS_TTL', 300),
    ],

    'active_statuses' => [
        'active',
        'trialing',
        'past_due',
        'incomplete',
        'incomplete_expired',
        'unpaid',
    ],

    'plans' => [
        'premium_monthly' => [
            'name' => 'Premium Monthly',
            'price_id' => env('STRIPE_MONTHLY_PRICE'),
            'amount' => (int) env('STRIPE_MONTHLY_AMOUNT', 999),
            'currency' => env('CASHIER_CURRENCY', 'usd'),
            'interval' => 'month',
            'interval_count' => 1,
            'features' => [
                'Unlimited streaming access',
                'New releases weekly',
            ],
        ],
        'premium_yearly' => [
            'name' => 'Premium Yearly',
            'price_id' => env('STRIPE_YEARLY_PRICE'),
            'amount' => (int) env('STRIPE_YEARLY_AMOUNT', 9999),
            'currency' => env('CASHIER_CURRENCY', 'usd'),
            'interval' => 'year',
            'interval_count' => 1,
            'features' => [
                'Two months free',
                'Exclusive annual perks',
            ],
        ],
    ],
];
