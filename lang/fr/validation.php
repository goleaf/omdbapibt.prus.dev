<?php

return [
    'The given data was invalid.' => 'Les données fournies ne sont pas valides.',
    '(and :count more error)' => '(et :count erreur supplémentaire)',
    '(and :count more errors)' => '(et :count erreurs supplémentaires)',
    'custom' => [
        'query' => [
            'required' => 'Veuillez fournir un terme de recherche.',
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
