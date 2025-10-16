<?php

return [
    'enum' => 'La :attribute seleccionada no es válida.',
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
        'keys' => [
            'required' => 'Debes proporcionar al menos una entrada de clave API de OMDb.',
            'array' => 'La carga de claves API de OMDb debe ser un arreglo.',
            'min' => 'Proporciona al menos una entrada de clave API de OMDb.',
        ],
        'keys_entries' => [
            'required' => 'Cada entrada de clave API de OMDb debe estar presente.',
            'array' => 'Cada entrada de clave API de OMDb debe ser un arreglo asociativo.',
        ],
        'keys_entry' => [
            'key' => [
                'required' => 'Se requiere un valor de clave API de OMDb.',
                'string' => 'La clave API de OMDb debe ser un valor de texto.',
                'size' => 'La clave API de OMDb debe tener exactamente :size caracteres.',
                'regex' => 'La clave API de OMDb solo puede contener dígitos y letras minúsculas.',
            ],
            'status' => [
                'string' => 'El estado de la clave API de OMDb debe ser un valor de texto.',
                'in' => 'El estado de la clave API de OMDb debe ser "pending", "valid", "invalid" o "unknown".',
            ],
            'first_seen_at' => [
                'date' => 'La marca de tiempo de primera detección debe ser una fecha válida.',
            ],
            'last_checked_at' => [
                'date' => 'La marca de tiempo de la última verificación debe ser una fecha válida.',
            ],
            'last_confirmed_at' => [
                'date' => 'La marca de tiempo de la última confirmación debe ser una fecha válida.',
            ],
            'last_response_code' => [
                'integer' => 'El código de respuesta más reciente debe ser un número entero.',
                'between' => 'El código de respuesta más reciente debe estar entre :min y :max.',
            ],
        ],
    ],
    'attributes' => [
        'query' => 'término de búsqueda',
        'limit' => 'límite de resultados',
        'keys' => 'carga de claves API de OMDb',
        'keys_entries' => 'entrada de clave API de OMDb',
        'keys_entry' => [
            'key' => 'clave API de OMDb',
            'status' => 'estado de la clave API de OMDb',
            'first_seen_at' => 'marca de tiempo de primera detección',
            'last_checked_at' => 'marca de tiempo de la última verificación',
            'last_confirmed_at' => 'marca de tiempo de la última confirmación',
            'last_response_code' => 'código de respuesta más reciente',
        ],
        'keys.*' => 'entrada de clave API de OMDb',
        'keys.*.key' => 'clave API de OMDb',
        'keys.*.status' => 'estado de la clave API de OMDb',
        'keys.*.first_seen_at' => 'marca de tiempo de primera detección',
        'keys.*.last_checked_at' => 'marca de tiempo de la última verificación',
        'keys.*.last_confirmed_at' => 'marca de tiempo de la última confirmación',
        'keys.*.last_response_code' => 'código de respuesta más reciente',
    ],
];
