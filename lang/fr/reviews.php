<?php

return [
    'validation' => [
        'movie_title' => [
            'required' => 'Veuillez saisir le titre du film.',
            'string' => 'Le titre du film doit être du texte.',
            'max' => 'Le titre du film ne peut pas dépasser :max caractères.',
        ],
        'rating' => [
            'required' => 'Veuillez choisir une note.',
            'integer' => 'La note doit être un nombre entier.',
            'between' => 'Choisissez une note entre :min et :max.',
        ],
        'body' => [
            'required' => 'Veuillez partager votre avis.',
            'string' => "L'avis doit être du texte.",
            'max' => "L'avis ne peut pas dépasser :max caractères.",
        ],
    ],
    'status' => [
        'submitted' => 'Votre avis a été envoyé avec succès.',
    ],
];
