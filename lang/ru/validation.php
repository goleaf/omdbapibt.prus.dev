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
        'keys' => [
            'required' => 'You must provide at least one OMDb API key entry.',
            'array' => 'The OMDb API key payload must be an array.',
            'min' => 'Provide at least one OMDb API key entry.',
        ],
        'keys_entries' => [
            'required' => 'Each OMDb API key entry must be present.',
            'array' => 'Each OMDb API key entry must be an associative array.',
        ],
        'keys_entry' => [
            'key' => [
                'required' => 'An OMDb API key value is required.',
                'string' => 'The OMDb API key must be a text value.',
                'size' => 'The OMDb API key must be exactly :size characters.',
                'regex' => 'The OMDb API key may only contain digits and lowercase letters.',
            ],
            'status' => [
                'string' => 'The OMDb API key status must be a text value.',
                'in' => 'The OMDb API key status must be pending, valid, invalid, or unknown.',
            ],
            'first_seen_at' => [
                'date' => 'The first seen timestamp must be a valid date.',
            ],
            'last_checked_at' => [
                'date' => 'The last checked timestamp must be a valid date.',
            ],
            'last_confirmed_at' => [
                'date' => 'The last confirmed timestamp must be a valid date.',
            ],
            'last_response_code' => [
                'integer' => 'The last response code must be a whole number.',
                'between' => 'The last response code must be between :min and :max.',
            ],
        ],
    ],
    'attributes' => [
        'query' => 'search query',
        'limit' => 'result limit',
        'keys' => 'OMDb API key payload',
        'keys_entries' => 'OMDb API key entry',
        'keys_entry' => [
            'key' => 'OMDb API key',
            'status' => 'OMDb API key status',
            'first_seen_at' => 'first seen timestamp',
            'last_checked_at' => 'last checked timestamp',
            'last_confirmed_at' => 'last confirmed timestamp',
            'last_response_code' => 'last response code',
        ],
        'keys.*' => 'OMDb API key entry',
        'keys.*.key' => 'OMDb API key',
        'keys.*.status' => 'OMDb API key status',
        'keys.*.first_seen_at' => 'first seen timestamp',
        'keys.*.last_checked_at' => 'last checked timestamp',
        'keys.*.last_confirmed_at' => 'last confirmed timestamp',
        'keys.*.last_response_code' => 'last response code',
    ],
];
