<?php

return [
    'enum' => 'The selected :attribute is invalid.',
    'custom' => [
        'workload' => [
            'required' => 'A parser workload is required.',
            'string' => 'The parser workload must be a string value.',
            'enum' => 'The selected parser workload is invalid.',
        ],
        'query' => [
            'required' => 'Please enter a search query.',
            'string' => 'The search query must be a text value.',
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
