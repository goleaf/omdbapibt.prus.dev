<?php

$baseUrl = rtrim(env('APP_URL', 'https://omdbstream.test'), '/');

return [
    'footer' => [
        'links' => [
            [
                'label' => 'ui.nav.footer.terms',
                'url' => env('TERMS_URL', $baseUrl.'/legal/terms'),
            ],
            [
                'label' => 'ui.nav.footer.privacy',
                'url' => env('PRIVACY_URL', $baseUrl.'/legal/privacy'),
            ],
            [
                'label' => 'ui.nav.footer.support',
                'url' => env('SUPPORT_URL', 'mailto:support@omdbstream.test'),
            ],
        ],
    ],
];
