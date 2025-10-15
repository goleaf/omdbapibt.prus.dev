<?php

return [
    'The given data was invalid.' => 'Les données fournies ne sont pas valides.',
    '(and :count more error)' => '(et :count erreur supplémentaire)',
    '(and :count more errors)' => '(et :count erreurs supplémentaires)',
    'enum' => 'La :attribute sélectionnée n\'est pas valide.',
    'custom' => [
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
        'workload' => [
            'required' => 'Veuillez sélectionner une charge de traitement à lancer.',
            'string' => 'La charge de traitement doit être fournie sous forme de texte.',
            'enum' => 'La charge de traitement sélectionnée n\'est pas valide.',
            'in' => 'La charge de traitement sélectionnée n\'est pas valide.',
        ],
    ],
    'attributes' => [
        'query' => 'terme de recherche',
        'limit' => 'limite de résultats',
        'workload' => 'charge de traitement',
    ],
];
