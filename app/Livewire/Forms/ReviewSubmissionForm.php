<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class ReviewSubmissionForm extends Form
{
    public string $movieTitle = '';

    public ?int $rating = 5;

    public string $body = '';

    public function rules(): array
    {
        return [
            'movieTitle' => ['required', 'string', 'max:255'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'movieTitle.required' => __('reviews.validation.movie_title.required'),
            'movieTitle.string' => __('reviews.validation.movie_title.string'),
            'movieTitle.max' => __('reviews.validation.movie_title.max'),
            'rating.required' => __('reviews.validation.rating.required'),
            'rating.integer' => __('reviews.validation.rating.integer'),
            'rating.between' => __('reviews.validation.rating.between'),
            'body.required' => __('reviews.validation.body.required'),
            'body.string' => __('reviews.validation.body.string'),
            'body.max' => __('reviews.validation.body.max'),
        ];
    }
}
