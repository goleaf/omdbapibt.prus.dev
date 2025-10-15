<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class MovieLookupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'min:2'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'query.required' => __('validation.movie_lookup.query.required'),
            'query.string' => __('validation.movie_lookup.query.string'),
            'query.min' => __('validation.movie_lookup.query.min'),
            'limit.integer' => __('validation.movie_lookup.limit.integer'),
            'limit.min' => __('validation.movie_lookup.limit.min'),
            'limit.max' => __('validation.movie_lookup.limit.max'),
        ];
    }
}
