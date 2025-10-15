<?php

return [
    'cache' => [
        'store' => env('UI_TRANSLATIONS_CACHE_STORE', 'redis'),
        'fallback_store' => env('UI_TRANSLATIONS_CACHE_FALLBACK', 'array'),
        'key' => env('UI_TRANSLATIONS_CACHE_KEY', 'ui_translations.lines'),
    ],
];
