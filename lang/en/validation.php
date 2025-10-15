<?php

return [
    'failed' => 'The provided data is invalid.',

    'movie_lookup' => [
        'query' => [
            'required' => 'Please provide a search term.',
            'string' => 'The search term must be text.',
            'min' => 'The search term must be at least :min characters.',
        ],
        'limit' => [
            'integer' => 'The result limit must be an integer.',
            'min' => 'The result limit must be at least :min.',
            'max' => 'The result limit may not be greater than :max.',
        ],
    ],

    'attributes' => [
        'query' => 'search term',
        'limit' => 'result limit',
    ],
];
