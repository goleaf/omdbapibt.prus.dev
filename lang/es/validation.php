<?php

return [
    'failed' => 'Los datos proporcionados no son válidos.',

    'movie_lookup' => [
        'query' => [
            'required' => 'Por favor, proporciona un término de búsqueda.',
            'string' => 'El término de búsqueda debe ser un texto.',
            'min' => 'El término de búsqueda debe tener al menos :min caracteres.',
        ],
        'limit' => [
            'integer' => 'El límite de resultados debe ser un número entero.',
            'min' => 'El límite de resultados debe ser al menos :min.',
            'max' => 'El límite de resultados no puede ser mayor que :max.',
        ],
    ],

    'attributes' => [
        'query' => 'término de búsqueda',
        'limit' => 'límite de resultados',
    ],
];
