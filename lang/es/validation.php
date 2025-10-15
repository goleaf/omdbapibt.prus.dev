<?php

return [
    'enum' => 'La opción seleccionada para :attribute no es compatible.',

    'attributes' => [
        'workload' => 'la carga de trabajo del parser',
    ],

    'parser_trigger' => [
        'workload' => [
            'required' => 'Selecciona una carga de trabajo del parser para poner en cola.',
            'string' => 'La carga de trabajo debe proporcionarse como un identificador de texto.',
            'enum' => 'La carga de trabajo del parser seleccionada no es compatible.',
        ],
    ],
];
