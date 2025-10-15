<?php

return [
    'enum' => 'La valeur sélectionnée pour :attribute n’est pas prise en charge.',

    'attributes' => [
        'workload' => 'la charge de travail du parseur',
    ],

    'parser_trigger' => [
        'workload' => [
            'required' => 'Sélectionnez une charge de travail du parseur à mettre en file d’attente.',
            'string' => 'La charge de travail doit être fournie sous forme d’identifiant texte.',
            'enum' => 'La charge de travail du parseur sélectionnée n’est pas prise en charge.',
        ],
    ],
];
