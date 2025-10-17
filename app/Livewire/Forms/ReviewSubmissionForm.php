<?php

namespace App\Livewire\Forms;

use Livewire\Form;

class ReviewSubmissionForm extends Form
{
    public ?int $movieId = null;

    public int $rating = 5;

    public string $body = '';

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'movieId' => ['required', 'integer', 'exists:movies,id'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'body' => ['required', 'string', 'max:2000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'movieId.required' => __('reviews.validation.movie_id.required'),
            'movieId.integer' => __('reviews.validation.movie_id.integer'),
            'movieId.exists' => __('reviews.validation.movie_id.exists'),
            'rating.required' => __('reviews.validation.rating.required'),
            'rating.integer' => __('reviews.validation.rating.integer'),
            'rating.between' => __('reviews.validation.rating.between'),
            'body.required' => __('reviews.validation.body.required'),
            'body.string' => __('reviews.validation.body.string'),
            'body.max' => __('reviews.validation.body.max'),
        ];
    }
}
