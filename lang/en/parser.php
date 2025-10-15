<?php

return [
    'moderation' => [
        'validation' => [
            'notes_required' => 'Add a short note explaining why this entry is being rejected.',
            'notes_max' => 'Rejection notes may not be longer than :max characters.',
        ],
        'fields' => [
            'notes' => 'rejection notes',
        ],
    ],
];
