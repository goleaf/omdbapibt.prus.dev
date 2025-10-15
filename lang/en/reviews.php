<?php

return [
    'messages' => [
        'submitted' => 'Review submitted successfully.',
    ],
    'validation' => [
        'movie_title' => [
            'required' => 'Please enter a movie title.',
            'string' => 'The movie title must be text.',
            'max' => 'Movie titles may not be greater than :max characters.',
        ],
        'rating' => [
            'required' => 'Please select a rating.',
            'integer' => 'The rating must be a whole number.',
            'between' => 'Choose a rating between :min and :max stars.',
        ],
        'body' => [
            'required' => 'Please share your thoughts about the movie.',
            'string' => 'The review must be text.',
            'max' => 'Reviews may not exceed :max characters.',
        ],
    ],
];
