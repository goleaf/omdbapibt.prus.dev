<?php

return [
    'validation' => [
        'movie_id' => [
            'required' => 'Please select a movie.',
            'integer' => 'The selected movie is invalid.',
            'exists' => 'The selected movie could not be found.',
        ],
        'rating' => [
            'required' => 'Please choose a rating.',
            'integer' => 'The rating must be a whole number.',
            'between' => 'Choose a rating between :min and :max.',
        ],
        'body' => [
            'required' => 'Please share your review.',
            'string' => 'The review must be text.',
            'max' => 'Reviews may not be greater than :max characters.',
        ],
    ],
    'status' => [
        'submitted' => 'Your review was submitted successfully.',
    ],
    'form' => [
        'movie_label' => 'Movie',
        'select_movie_placeholder' => 'Select a movie',
    ],
    'labels' => [
        'unknown_movie' => 'Unknown movie',
    ],
];
