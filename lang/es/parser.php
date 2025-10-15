<?php

return [
    'moderation' => [
        'validation' => [
            'notes_required' => 'Agrega una nota breve que explique por qué se rechaza esta entrada.',
            'notes_max' => 'Las notas de rechazo no pueden superar los :max caracteres.',
        ],
        'fields' => [
            'notes' => 'notas de rechazo',
        ],
    ],
];
