<?php

return [
    'custom' => [
        'workload' => [
            'required' => 'Se requiere una carga de trabajo del parser.',
            'string' => 'La carga de trabajo del parser debe ser una cadena.',
            'enum' => 'La carga de trabajo del parser seleccionada no es válida.',
        ],
        'query' => [
            'required' => 'Por favor, ingresa un término de búsqueda.',
            'string' => 'El término de búsqueda debe ser un valor de texto.',
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
