<?php

return [
    'The given data was invalid.' => 'Los datos proporcionados no son válidos.',
    '(and :count more error)' => '(y :count error adicional)',
    '(and :count more errors)' => '(y :count errores adicionales)',
    'custom' => [
        'query' => [
            'required' => 'Por favor ingresa un término de búsqueda.',
            'string' => 'El término de búsqueda debe ser una cadena de texto.',
            'min' => 'El término de búsqueda debe tener al menos :min caracteres.',
        ],
        'limit' => [
            'integer' => 'El límite de resultados debe ser un número entero.',
            'min' => 'El límite de resultados debe ser al menos :min.',
            'max' => 'El límite de resultados no puede ser mayor a :max.',
        ],
    ],
    'attributes' => [
        'query' => 'término de búsqueda',
        'limit' => 'límite de resultados',
    ],
];
