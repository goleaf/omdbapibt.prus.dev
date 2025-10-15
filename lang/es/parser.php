<?php

return [
    'moderation' => [
        'decision' => [
            'heading' => 'Decisión',
            'description' => 'Agrega contexto a tu decisión. Las notas son obligatorias cuando rechazas una entrada.',
            'fields' => [
                'notes' => 'Notas',
            ],
            'placeholder' => [
                'notes' => 'Resume por qué este payload debe aprobarse o rechazarse',
            ],
            'actions' => [
                'approve' => 'Aprobar y guardar',
                'reject' => 'Rechazar entrada',
            ],
        ],
        'validation' => [
            'notes' => [
                'required' => 'Incluye una nota de rechazo antes de continuar.',
                'string' => 'Las notas de rechazo deben ser texto sin formato.',
                'max' => 'Las notas de rechazo no pueden superar los :max caracteres.',
            ],
        ],
    ],
];
