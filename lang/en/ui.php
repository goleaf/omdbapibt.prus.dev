<?php

return [
    'nav' => [
        'brand' => [
            'primary' => 'OMDb',
            'secondary' => 'Stream',
        ],
        'links' => [
            'home' => 'Home',
            'browse' => 'Browse',
            'pricing' => 'Pricing',
            'components' => 'Components',
            'account' => 'Account',
            'admin' => 'Admin',
        ],
        'auth' => [
            'login' => 'Sign in',
            'register' => 'Join now',
            'logout' => 'Logout',
        ],
        'theme' => [
            'light' => 'Light mode',
            'dark' => 'Dark mode',
        ],
        'theme_toggle' => 'Toggle theme',
        'footer' => [
            'terms' => 'Terms',
            'privacy' => 'Privacy',
            'support' => 'Support',
            'copyright' => '© :year OMDb Stream. All rights reserved.',
        ],
    ],
    'dashboard' => [
        'title' => 'Dashboard',
        'nav' => [
            'overview' => 'Overview',
            'manage_subscription' => 'Manage Subscription',
        ],
        'welcome_heading' => 'Welcome back!',
        'welcome_body' => 'Review your plan details, manage billing, and make changes to your subscription in real time.',
        'trial' => [
            'active_title' => 'Your free trial is active.',
            'active_body' => 'Enjoy full access until :date. We\'ll send reminders before billing begins.',
            'cta' => 'Start :days-day trial',
            'intro_title' => 'Start your :days-day free trial.',
            'intro_body' => 'Unlock every movie detail, premium filters, and curated recommendations while you evaluate the platform.',
            'missing_price' => 'Add your Stripe price identifier to :key to enable subscriptions.',
            'cancel_notice' => 'Cancel any time before the trial ends to avoid charges.',
        ],
        'subscriber' => [
            'thanks_title' => 'Thanks for being a subscriber!',
            'thanks_body' => 'Enjoy unlimited access to detailed data, watchlists, and personalized insights.',
        ],
        'grace' => [
            'title' => 'Your subscription is scheduled to end.',
            'body' => 'Access remains available until :date. Resume the plan in Stripe if you change your mind.',
        ],
        'inactive' => [
            'title' => 'Subscription inactive.',
            'body' => 'Re-subscribe anytime from the billing portal to regain premium access.',
        ],
    ],
    'filters' => [
        'heading' => 'Advanced filters',
        'description' => 'Tune your discovery feed with genres, languages, and release years.',
        'type_label' => 'Type',
        'types' => [
            'movies' => 'Movies',
            'shows' => 'TV Shows',
        ],
        'genre_label' => 'Genre',
        'year_label' => 'Year',
        'language_label' => 'Language',
        'sort_label' => 'Sort by',
        'sort_options' => [
            'popularity_desc' => 'Popularity',
            'vote_average_desc' => 'Rating',
            'release_date_desc' => 'Newest',
            'release_date_asc' => 'Oldest',
        ],
        'results_title' => 'Results preview',
        'results_summary' => 'Filtering :genre :type released in :year.',
        'apply' => 'Apply',
    ],
];
