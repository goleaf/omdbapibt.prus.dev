<?php

return [
    'default_chunk' => (int) env('PARSER_DEFAULT_CHUNK', 25),

    'targets' => [
        'movies' => [
            'label' => 'movies',
            'chunk_size' => (int) env('PARSER_MOVIE_CHUNK', 50),
            'hydrator' => App\Services\Parser\Hydrators\MovieHydrator::class,
        ],

        'tv' => [
            'label' => 'TV shows',
            'chunk_size' => (int) env('PARSER_TV_CHUNK', 25),
            'hydrator' => App\Services\Parser\Hydrators\TvShowHydrator::class,
        ],

        'people' => [
            'label' => 'people',
            'chunk_size' => (int) env('PARSER_PEOPLE_CHUNK', 40),
            'hydrator' => App\Services\Parser\Hydrators\PersonHydrator::class,
        ],
    ],
];
