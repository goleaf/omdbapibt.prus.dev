<?php

return [
    'messages' => [
        'submitted' => 'Reseña enviada correctamente.',
    ],
    'validation' => [
        'movie_title' => [
            'required' => 'Introduce el título de la película.',
            'string' => 'El título de la película debe ser texto.',
            'max' => 'El título de la película no puede superar los :max caracteres.',
        ],
        'rating' => [
            'required' => 'Selecciona una calificación.',
            'integer' => 'La calificación debe ser un número entero.',
            'between' => 'Elige una calificación entre :min y :max estrellas.',
        ],
        'body' => [
            'required' => 'Comparte tus opiniones sobre la película.',
            'string' => 'La reseña debe ser texto.',
            'max' => 'La reseña no puede superar los :max caracteres.',
        ],
    ],
];
