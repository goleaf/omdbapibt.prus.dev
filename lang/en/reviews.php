<?php

return [
    'validation' => [
        'movie_title' => [
            'required' => 'Please enter the movie title.',
            'string' => 'The movie title must be text.',
            'max' => 'The movie title may not be greater than :max characters.',
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
];
