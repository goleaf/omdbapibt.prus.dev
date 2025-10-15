<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'prices' => [
            'monthly' => env('STRIPE_MONTHLY_PRICE'),
            'yearly' => env('STRIPE_YEARLY_PRICE'),
        ],
        'trial_days' => (int) env('STRIPE_TRIAL_DAYS', 7),
    ],

    'tmdb' => [
        'key' => env('TMDB_API_KEY'),
        'base_url' => rtrim(env('TMDB_BASE_URL', 'https://api.themoviedb.org/3'), '/').'/',
        'query' => [
            'api_key' => env('TMDB_API_KEY'),
        ],
    ],

    'omdb' => [
        'key' => env('OMDB_API_KEY'),
        'base_url' => rtrim(env('OMDB_BASE_URL', 'https://www.omdbapi.com'), '/').'/',
        'query' => [
            'apikey' => env('OMDB_API_KEY'),
        ],
        'max_requests_per_minute' => (int) env('OMDB_MAX_REQUESTS_PER_MINUTE', 60),
    ],

    'justwatch' => [
        'base_url' => env('JUSTWATCH_BASE_URL', 'https://apis.justwatch.com/content'),
        'headers' => array_filter([
            'Authorization' => env('JUSTWATCH_API_KEY')
                ? 'Bearer '.env('JUSTWATCH_API_KEY')
                : null,
        ]),
    ],

    'audiodb' => [
        'base_url' => env('AUDIODB_BASE_URL', 'https://theaudiodb.com/api/v1/json'),
        'query' => array_filter([
            'apikey' => env('AUDIODB_API_KEY'),
        ]),
    ],

];
