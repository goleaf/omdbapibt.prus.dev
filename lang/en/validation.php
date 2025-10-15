<?php

return [
    'The given data was invalid.' => 'The given data was invalid.',
    '(and :count more error)' => '(and :count more error)',
    '(and :count more errors)' => '(and :count more errors)',
    'enum' => 'The selected :attribute is invalid.',
    'custom' => [
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
        'workload' => [
            'required' => 'Please select a parser workload to trigger.',
            'string' => 'The parser workload must be provided as text.',
            'enum' => 'The selected parser workload is invalid.',
            'in' => 'The selected parser workload is invalid.',
        ],
    ],
    'attributes' => [
        'query' => 'search query',
        'limit' => 'result limit',
        'workload' => 'parser workload',
    ],
];
