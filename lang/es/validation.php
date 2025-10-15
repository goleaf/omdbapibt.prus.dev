<?php

return [
    'The given data was invalid.' => 'Los datos proporcionados no son válidos.',
    '(and :count more error)' => '(y :count error adicional)',
    '(and :count more errors)' => '(y :count errores adicionales)',
    'custom' => [
        'query' => [
            'required' => 'Por favor ingresa un término de búsqueda.',
            'min' => 'El término de búsqueda debe tener al menos :min caracteres.',
        ],
        'limit' => [
            'integer' => 'El límite de resultados debe ser un número entero.',
            'min' => 'El límite de resultados debe ser al menos :min.',
            'max' => 'El límite de resultados no puede ser mayor a :max.',
        ],
        'workload' => [
            'required' => 'Selecciona una carga de trabajo para ejecutar.',
            'string' => 'El valor de la carga de trabajo debe ser una cadena.',
            'enum' => 'La carga de trabajo seleccionada no es válida.',
        ],
    ],
    'attributes' => [
        'query' => 'término de búsqueda',
        'limit' => 'límite de resultados',
        'workload' => 'carga de trabajo',
    ],
];
