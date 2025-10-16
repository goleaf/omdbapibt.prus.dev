<?php

return [
    'validation' => [
        'movie_title' => [
            'required' => 'Por favor escribe el título de la película.',
            'string' => 'El título de la película debe ser texto.',
            'max' => 'El título de la película no puede tener más de :max caracteres.',
        ],
        'rating' => [
            'required' => 'Por favor elige una calificación.',
            'integer' => 'La calificación debe ser un número entero.',
            'between' => 'Elige una calificación entre :min y :max.',
        ],
        'body' => [
            'required' => 'Por favor comparte tu reseña.',
            'string' => 'La reseña debe ser texto.',
            'max' => 'La reseña no puede tener más de :max caracteres.',
        ],
    ],
    'status' => [
        'submitted' => 'Tu reseña se envió correctamente.',
    ],
];
