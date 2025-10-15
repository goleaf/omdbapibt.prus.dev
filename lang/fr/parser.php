<?php

return [
    'moderation' => [
        'validation' => [
            'notes_required' => 'Ajoutez une note expliquant pourquoi cette entrée est rejetée.',
            'notes_max' => 'La note de rejet ne doit pas dépasser :max caractères.',
        ],
        'fields' => [
            'notes' => 'note de rejet',
        ],
    ],
];
