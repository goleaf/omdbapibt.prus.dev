<?php

return [
    'moderation' => [
        'decision' => [
            'heading' => 'Decision',
            'description' => 'Leave context for your decision. Notes are required when rejecting an entry.',
            'fields' => [
                'notes' => 'Notes',
            ],
            'placeholder' => [
                'notes' => 'Summarize why this payload should be approved or rejected',
            ],
            'actions' => [
                'approve' => 'Approve and persist',
                'reject' => 'Reject entry',
            ],
        ],
        'validation' => [
            'notes' => [
                'required' => 'Please provide a rejection note before continuing.',
                'string' => 'Rejection notes must be plain text.',
                'max' => 'Rejection notes may not be longer than :max characters.',
            ],
        ],
    ],
    'trigger' => [
        'validation' => [
            'workload' => [
                'required' => 'Please choose a parser workload before continuing.',
                'string' => 'The parser workload must be provided as a string.',
                'enum' => 'The selected parser workload is not supported.',
            ],
        ],
        'attributes' => [
            'workload' => 'parser workload',
        ],
    ],
];
