<?php

namespace App\Http\Requests;

use App\Models\OmdbApiKey;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class ImportOmdbKeysRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, \Illuminate\Contracts\Validation\Rule|\Illuminate\Validation\Rules\In|string>>
     */
    public function rules(): array
    {
        return [
            'keys' => ['required', 'array', 'min:1'],
            'keys.*' => ['required', 'array'],
            'keys.*.key' => ['required', 'string', 'size:8', 'regex:/^[0-9a-z]{8}$/'],
            'keys.*.status' => ['nullable', 'string', Rule::in([
                OmdbApiKey::STATUS_PENDING,
                OmdbApiKey::STATUS_VALID,
                OmdbApiKey::STATUS_INVALID,
                OmdbApiKey::STATUS_UNKNOWN,
            ])],
            'keys.*.first_seen_at' => ['nullable', 'date'],
            'keys.*.last_checked_at' => ['nullable', 'date'],
            'keys.*.last_confirmed_at' => ['nullable', 'date'],
            'keys.*.last_response_code' => ['nullable', 'integer', 'between:100,599'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keys.required' => __('validation.custom.keys.required'),
            'keys.array' => __('validation.custom.keys.array'),
            'keys.min' => __('validation.custom.keys.min'),
            'keys.*.required' => __('validation.custom.keys_entries.required'),
            'keys.*.array' => __('validation.custom.keys_entries.array'),
            'keys.*.key.required' => __('validation.custom.keys_entry.key.required'),
            'keys.*.key.string' => __('validation.custom.keys_entry.key.string'),
            'keys.*.key.size' => __('validation.custom.keys_entry.key.size'),
            'keys.*.key.regex' => __('validation.custom.keys_entry.key.regex'),
            'keys.*.status.string' => __('validation.custom.keys_entry.status.string'),
            'keys.*.status.in' => __('validation.custom.keys_entry.status.in'),
            'keys.*.first_seen_at.date' => __('validation.custom.keys_entry.first_seen_at.date'),
            'keys.*.last_checked_at.date' => __('validation.custom.keys_entry.last_checked_at.date'),
            'keys.*.last_confirmed_at.date' => __('validation.custom.keys_entry.last_confirmed_at.date'),
            'keys.*.last_response_code.integer' => __('validation.custom.keys_entry.last_response_code.integer'),
            'keys.*.last_response_code.between' => __('validation.custom.keys_entry.last_response_code.between'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'keys' => __('validation.attributes.keys'),
            'keys.*' => __('validation.attributes.keys_entries'),
            'keys.*.key' => __('validation.attributes.keys_entry.key'),
            'keys.*.status' => __('validation.attributes.keys_entry.status'),
            'keys.*.first_seen_at' => __('validation.attributes.keys_entry.first_seen_at'),
            'keys.*.last_checked_at' => __('validation.attributes.keys_entry.last_checked_at'),
            'keys.*.last_confirmed_at' => __('validation.attributes.keys_entry.last_confirmed_at'),
            'keys.*.last_response_code' => __('validation.attributes.keys_entry.last_response_code'),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function keys(): array
    {
        /** @var array<int, array<string, mixed>> $keys */
        $keys = $this->validated('keys', []);

        return $keys;
    }

    protected function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        throw new HttpResponseException(response()->json([
            'message' => $errors->first(),
            'errors' => $errors->toArray(),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }

    /**
     * Provide human readable request body details for Scribe.
     *
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'keys' => [
                'description' => 'List of OMDb API key payloads that should be imported.',
                'type' => 'array',
                'example' => [
                    [
                        'key' => 'abcd1234',
                        'status' => OmdbApiKey::STATUS_PENDING,
                        'first_seen_at' => '2024-01-01T00:00:00Z',
                        'last_checked_at' => '2024-01-02T13:45:00Z',
                        'last_confirmed_at' => null,
                        'last_response_code' => 200,
                    ],
                ],
            ],
            'keys.*.key' => [
                'description' => 'Eight-character OMDb API key consisting only of lowercase letters and digits.',
                'example' => 'abcd1234',
                'type' => 'string',
            ],
            'keys.*.status' => [
                'description' => 'Optional status that should be assigned to the imported key.',
                'example' => OmdbApiKey::STATUS_PENDING,
                'type' => 'string',
                'enumValues' => [
                    OmdbApiKey::STATUS_PENDING,
                    OmdbApiKey::STATUS_VALID,
                    OmdbApiKey::STATUS_INVALID,
                    OmdbApiKey::STATUS_UNKNOWN,
                ],
            ],
            'keys.*.first_seen_at' => [
                'description' => 'ISO 8601 timestamp of when the key was first discovered.',
                'example' => '2024-01-01T00:00:00Z',
                'type' => 'string',
            ],
            'keys.*.last_checked_at' => [
                'description' => 'ISO 8601 timestamp of the most recent validation attempt.',
                'example' => '2024-01-02T13:45:00Z',
                'type' => 'string',
            ],
            'keys.*.last_confirmed_at' => [
                'description' => 'ISO 8601 timestamp of the most recent successful confirmation.',
                'example' => '2024-01-03T18:30:00Z',
                'type' => 'string',
            ],
            'keys.*.last_response_code' => [
                'description' => 'HTTP status code that was returned by the OMDb API during the latest check.',
                'example' => 401,
                'type' => 'integer',
            ],
        ];
    }
}
