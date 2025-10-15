<?php

return [
    'collections' => [
        'spotlight-premieres' => [
            'label' => 'Spotlight Premieres',
            'tagline' => 'Remastered festival darlings arriving this week.',
            'description' => 'Fresh transfers and director-approved editions selected nightly by our programming team.',
            'featured_slugs' => [
                'the-galactic-voyage',
                'lunar-echoes',
                'eclipse-station',
            ],
            'minimum_rating' => 7.2,
            'released_within_days' => 120,
            'sort' => [
                'release_date' => 'desc',
                'popularity' => 'desc',
            ],
        ],
        'critics-choice' => [
            'label' => "Critics' Choice",
            'tagline' => 'Certified picks with glowing reviews across the globe.',
            'description' => 'High-scoring films from trusted critics featuring award-season standouts and cult follow-ups.',
            'minimum_rating' => 8.0,
            'minimum_popularity' => 35,
            'genre_slugs' => ['drama', 'mystery'],
            'sort' => [
                'vote_average' => 'desc',
                'release_date' => 'desc',
            ],
        ],
        'late-night-deep-cuts' => [
            'label' => 'Late Night Deep Cuts',
            'tagline' => 'Cult sleepers and midnight discoveries built for flux marathons.',
            'description' => 'A rolling mix of science fiction, retro thrillers, and neon noir essentials sourced from our archive.',
            'genre_slugs' => ['science-fiction', 'thriller'],
            'language_codes' => ['en', 'ja'],
            'released_after_year' => 1995,
            'sort' => [
                'popularity' => 'desc',
                'title' => 'asc',
            ],
        ],
    ],
];
