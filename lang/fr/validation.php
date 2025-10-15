<?php

return [
    'failed' => 'Les données fournies ne sont pas valides.',

    'movie_lookup' => [
        'query' => [
            'required' => 'Veuillez fournir un terme de recherche.',
            'string' => 'Le terme de recherche doit être un texte.',
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
