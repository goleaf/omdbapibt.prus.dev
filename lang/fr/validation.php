<?php

return [
    'custom' => [
        'workload' => [
            'required' => 'Une charge de travail du parseur est requise.',
            'string' => 'La charge de travail du parseur doit être une chaîne.',
            'enum' => 'La charge de travail du parseur sélectionnée n\'est pas valide.',
        ],
        'query' => [
            'required' => 'Veuillez fournir un terme de recherche.',
            'string' => 'Le terme de recherche doit être une chaîne de caractères.',
            'min' => 'Le terme de recherche doit contenir au moins :min caractères.',
        ],
        'limit' => [
            'integer' => 'La limite de résultats doit être un nombre entier.',
            'min' => 'La limite de résultats doit être au moins de :min.',
            'max' => 'La limite de résultats ne peut pas dépasser :max.',
        ],
    ],
    'attributes' => [
        'query' => 'terme de recherche',
        'limit' => 'limite de résultats',
    ],
];
