<?php

return [
    'validation' => [
        'movie_id' => [
            'required' => 'Veuillez sélectionner un film.',
            'integer' => 'Le film sélectionné est invalide.',
            'exists' => 'Le film sélectionné est introuvable.',
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
    'form' => [
        'movie_label' => 'Film',
        'select_movie_placeholder' => 'Sélectionnez un film',
    ],
    'labels' => [
        'unknown_movie' => 'Film inconnu',
    ],
];
