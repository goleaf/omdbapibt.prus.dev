<?php

return [
    'validation' => [
        'movie_id' => [
            'required' => 'Por favor selecciona una película.',
            'integer' => 'La película seleccionada no es válida.',
            'exists' => 'No se encontró la película seleccionada.',
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
    'form' => [
        'movie_label' => 'Película',
        'select_movie_placeholder' => 'Selecciona una película',
    ],
    'labels' => [
        'unknown_movie' => 'Película desconocida',
    ],
];
