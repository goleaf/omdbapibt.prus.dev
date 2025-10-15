<?php

return [
    'movie_lookup' => [
        'max_attempts' => (int) env('RATE_LIMIT_MOVIE_LOOKUP_MAX_ATTEMPTS', 60),
        'decay_seconds' => (int) env('RATE_LIMIT_MOVIE_LOOKUP_DECAY_SECONDS', 60),
    ],

    'parser_trigger' => [
        'max_attempts' => (int) env('RATE_LIMIT_PARSER_TRIGGER_MAX_ATTEMPTS', 10),
        'decay_seconds' => (int) env('RATE_LIMIT_PARSER_TRIGGER_DECAY_SECONDS', 60),
    ],
];
