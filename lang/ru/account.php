<?php

return [
    'profile' => [
        'meta' => [
            'title' => 'Profile',
            'header' => 'Your profile',
            'subheader' => 'See the preferences, favorites, and social connections that shape your recommendations.',
        ],
        'values' => [
            'not_set' => 'Not set',
            'enabled' => 'Enabled',
            'disabled' => 'Disabled',
            'subscribed' => 'Subscribed',
            'unsubscribed' => 'Unsubscribed',
            'opted_in' => 'Opted in',
            'opted_out' => 'Opted out',
        ],
        'sections' => [
            'preferences' => [
                'title' => 'Preferences',
                'description' => 'Language, playback, and communications settings.',
                'empty' => 'No preferences configured yet.',
                'items' => [
                    'preferred_interface_language' => 'Preferred interface language',
                    'preferred_audio_language' => 'Preferred audio language',
                    'preferred_subtitle_language' => 'Preferred subtitle language',
                    'content_maturity' => 'Content maturity filter',
                    'autoplay_next_episode' => 'Autoplay next episode',
                    'autoplay_trailers' => 'Autoplay trailers on browse',
                    'newsletter_opt_in' => 'Product newsletter',
                    'marketing_opt_in' => 'Partner promotions',
                ],
            ],
            'favorites' => [
                'title' => 'Favorites',
                'description' => 'Your go-to genres, stories, and creators.',
                'empty' => 'Favorites will appear once you share them.',
                'items' => [
                    'favorite_genre' => 'Favorite genre',
                    'favorite_movie' => 'Favorite movie',
                    'favorite_tv_show' => 'Favorite series',
                    'favorite_actor' => 'Favorite performer',
                    'favorite_director' => 'Favorite director',
                    'favorite_quote' => 'Favorite quote',
                ],
            ],
            'personal' => [
                'title' => 'Personal info',
                'description' => 'Details that personalize notifications and recommendations.',
                'empty' => 'Personal details are not yet configured.',
                'items' => [
                    'display_name' => 'Display name',
                    'tagline' => 'Tagline',
                    'location' => 'Location',
                    'timezone' => 'Timezone',
                    'birthday' => 'Birthday',
                    'bio' => 'Bio',
                    'discord_handle' => 'Discord handle',
                ],
            ],
            'social' => [
                'title' => 'Social links',
                'description' => 'Connect your profiles and creative hubs.',
                'empty' => 'Add social profiles to share your watchlist with friends.',
                'items' => [
                    'website_url' => 'Personal website',
                    'twitter_url' => 'Twitter',
                    'instagram_url' => 'Instagram',
                    'tiktok_url' => 'TikTok',
                    'youtube_url' => 'YouTube',
                    'letterboxd_url' => 'Letterboxd',
                ],
            ],
        ],
    ],
];
