<?php

return [
    'enum' => "La :attribute sélectionnée n'est pas valide.",
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
        'keys' => [
            'required' => 'Vous devez fournir au moins une entrée de clé API OMDb.',
            'array' => 'La charge utile des clés API OMDb doit être un tableau.',
            'min' => 'Fournissez au moins une entrée de clé API OMDb.',
        ],
        'keys_entries' => [
            'required' => 'Chaque entrée de clé API OMDb doit être présente.',
            'array' => 'Chaque entrée de clé API OMDb doit être un tableau associatif.',
        ],
        'keys_entry' => [
            'key' => [
                'required' => 'Une valeur de clé API OMDb est requise.',
                'string' => 'La clé API OMDb doit être une valeur textuelle.',
                'size' => 'La clé API OMDb doit compter exactement :size caractères.',
                'regex' => 'La clé API OMDb ne peut contenir que des chiffres et des lettres minuscules.',
            ],
            'status' => [
                'string' => 'Le statut de la clé API OMDb doit être une valeur textuelle.',
                'in' => 'Le statut de la clé API OMDb doit être « pending », « valid », « invalid » ou « unknown ».',
            ],
            'first_seen_at' => [
                'date' => 'L\'horodatage de première détection doit être une date valide.',
            ],
            'last_checked_at' => [
                'date' => 'L\'horodatage de la dernière vérification doit être une date valide.',
            ],
            'last_confirmed_at' => [
                'date' => 'L\'horodatage de la dernière confirmation doit être une date valide.',
            ],
            'last_response_code' => [
                'integer' => 'Le dernier code de réponse doit être un nombre entier.',
                'between' => 'Le dernier code de réponse doit être compris entre :min et :max.',
            ],
        ],
    ],
    'attributes' => [
        'query' => 'terme de recherche',
        'limit' => 'limite de résultats',
        'keys' => 'charge utile des clés API OMDb',
        'keys_entries' => 'entrée de clé API OMDb',
        'keys_entry' => [
            'key' => 'clé API OMDb',
            'status' => 'statut de la clé API OMDb',
            'first_seen_at' => 'horodatage de première détection',
            'last_checked_at' => 'horodatage de la dernière vérification',
            'last_confirmed_at' => 'horodatage de la dernière confirmation',
            'last_response_code' => 'dernier code de réponse',
        ],
        'keys.*' => 'entrée de clé API OMDb',
        'keys.*.key' => 'clé API OMDb',
        'keys.*.status' => 'statut de la clé API OMDb',
        'keys.*.first_seen_at' => 'horodatage de première détection',
        'keys.*.last_checked_at' => 'horodatage de la dernière vérification',
        'keys.*.last_confirmed_at' => 'horodatage de la dernière confirmation',
        'keys.*.last_response_code' => 'dernier code de réponse',
    ],
];
