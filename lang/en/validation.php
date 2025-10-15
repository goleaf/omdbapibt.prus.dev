<?php

return [
    'enum' => 'The selected :attribute is not supported.',

    'attributes' => [
        'workload' => 'parser workload',
    ],

    'parser_trigger' => [
        'workload' => [
            'required' => 'Select a parser workload to queue.',
            'string' => 'The workload must be provided as a string identifier.',
            'enum' => 'The selected parser workload is not supported.',
        ],
    ],
];
