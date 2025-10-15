<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

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
            'query.required' => __('validation.custom.query.required'),
            'query.string' => __('validation.custom.query.string'),
            'query.min' => __('validation.custom.query.min'),
            'limit.integer' => __('validation.custom.limit.integer'),
            'limit.min' => __('validation.custom.limit.min'),
            'limit.max' => __('validation.custom.limit.max'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'query' => __('validation.attributes.query'),
            'limit' => __('validation.attributes.limit'),
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'message' => $errors->first(),
            'errors' => $errors->toArray(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
