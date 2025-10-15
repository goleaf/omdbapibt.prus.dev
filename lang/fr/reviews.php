<?php

return [
    'messages' => [
        'submitted' => 'Critique envoyée avec succès.',
    ],
    'validation' => [
        'movie_title' => [
            'required' => 'Veuillez saisir le titre du film.',
            'string' => 'Le titre du film doit être du texte.',
            'max' => 'Le titre du film ne peut pas dépasser :max caractères.',
        ],
        'rating' => [
            'required' => 'Veuillez sélectionner une note.',
            'integer' => 'La note doit être un nombre entier.',
            'between' => 'Choisissez une note comprise entre :min et :max étoiles.',
        ],
        'body' => [
            'required' => 'Partagez votre avis sur le film.',
            'string' => 'La critique doit être du texte.',
            'max' => 'La critique ne peut pas dépasser :max caractères.',
        ],
    ],
];
