<?php

return [
    'The given data was invalid.' => 'The given data was invalid.',
    '(and :count more error)' => '(and :count more error)',
    '(and :count more errors)' => '(and :count more errors)',
    'custom' => [
        'query' => [
            'required' => 'Please enter a search query.',
            'min' => 'The search query must be at least :min characters.',
        ],
        'limit' => [
            'integer' => 'The result limit must be a whole number.',
            'min' => 'The result limit must be at least :min.',
            'max' => 'The result limit may not be greater than :max.',
        ],
    ],
    'attributes' => [
        'query' => 'search query',
        'limit' => 'result limit',
    ],
];
