<?php

namespace Tests\Unit\Http\Requests;

use App\Http\Requests\ImportOmdbKeysRequest;
use App\Models\OmdbApiKey;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\In;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ImportOmdbKeysRequestTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        app()->setLocale('en');
    }

    public function test_it_authorizes_any_user(): void
    {
        $request = new ImportOmdbKeysRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_defines_expected_rules(): void
    {
        $request = new ImportOmdbKeysRequest;

        $rules = $request->rules();

        $this->assertSame(['required', 'array', 'min:1'], $rules['keys']);
        $this->assertSame(['required', 'array'], $rules['keys.*']);
        $this->assertSame(['required', 'string', 'size:8', 'regex:/^[0-9a-z]{8}$/'], $rules['keys.*.key']);

        $statusRules = $rules['keys.*.status'];
        $this->assertSame(['nullable', 'string'], array_slice($statusRules, 0, 2));
        $this->assertInstanceOf(In::class, $statusRules[2]);
        $this->assertSame('in:"pending","valid","invalid","unknown"', (string) $statusRules[2]);

        $this->assertSame(['nullable', 'date'], $rules['keys.*.first_seen_at']);
        $this->assertSame(['nullable', 'date'], $rules['keys.*.last_checked_at']);
        $this->assertSame(['nullable', 'date'], $rules['keys.*.last_confirmed_at']);
        $this->assertSame(['nullable', 'integer', 'between:100,599'], $rules['keys.*.last_response_code']);
    }

    public function test_it_accepts_valid_payloads(): void
    {
        $request = new ImportOmdbKeysRequest;

        $payload = [
            'keys' => [[
                'key' => 'abcd1234',
                'status' => OmdbApiKey::STATUS_PENDING,
                'first_seen_at' => now()->subDay()->toIso8601String(),
                'last_checked_at' => now()->toIso8601String(),
                'last_confirmed_at' => now()->toIso8601String(),
                'last_response_code' => 200,
            ]],
        ];

        $validator = Validator::make(
            $payload,
            $request->rules(),
            $request->messages(),
            $request->attributes()
        );

        $this->assertTrue($validator->passes());
        $this->assertSame($payload, $validator->validated());
    }

    #[DataProvider('invalidPayloadProvider')]
    public function test_it_rejects_invalid_payloads(array $payload, string $expectedField, string $expectedMessageKey, array $parameters = []): void
    {
        $request = new ImportOmdbKeysRequest;

        $validator = Validator::make(
            $payload,
            $request->rules(),
            $request->messages(),
            $request->attributes()
        );

        $this->assertFalse($validator->passes());
        $this->assertSame(__($expectedMessageKey, $parameters), $validator->errors()->first($expectedField));
    }

    public static function invalidPayloadProvider(): array
    {
        return [
            'missing keys' => [
                [],
                'keys',
                'validation.custom.keys.required',
            ],
            'payload not array' => [
                ['keys' => 'abc'],
                'keys',
                'validation.custom.keys.array',
            ],
            'empty array' => [
                ['keys' => []],
                'keys',
                'validation.custom.keys.required',
            ],
            'entry not array' => [
                ['keys' => ['abcd1234']],
                'keys.0',
                'validation.custom.keys_entries.array',
            ],
            'missing key value' => [
                ['keys' => [[]]],
                'keys.0.key',
                'validation.custom.keys_entry.key.required',
            ],
            'invalid key format' => [
                ['keys' => [[
                    'key' => 'ABC12345',
                ]]],
                'keys.0.key',
                'validation.custom.keys_entry.key.regex',
            ],
            'invalid status' => [
                ['keys' => [[
                    'key' => 'abcd1234',
                    'status' => 'revoked',
                ]]],
                'keys.0.status',
                'validation.custom.keys_entry.status.in',
            ],
            'invalid first seen timestamp' => [
                ['keys' => [[
                    'key' => 'abcd1234',
                    'first_seen_at' => 'not-a-date',
                ]]],
                'keys.0.first_seen_at',
                'validation.custom.keys_entry.first_seen_at.date',
            ],
            'invalid response code' => [
                ['keys' => [[
                    'key' => 'abcd1234',
                    'last_response_code' => 42,
                ]]],
                'keys.0.last_response_code',
                'validation.custom.keys_entry.last_response_code.between',
                ['min' => 100, 'max' => 599],
            ],
        ];
    }

    public function test_messages_are_localized_in_english(): void
    {
        $request = new ImportOmdbKeysRequest;

        $this->assertSame([
            'keys.required' => 'You must provide at least one OMDb API key entry.',
            'keys.array' => 'The OMDb API key payload must be an array.',
            'keys.min' => 'Provide at least one OMDb API key entry.',
            'keys.*.required' => 'Each OMDb API key entry must be present.',
            'keys.*.array' => 'Each OMDb API key entry must be an associative array.',
            'keys.*.key.required' => 'An OMDb API key value is required.',
            'keys.*.key.string' => 'The OMDb API key must be a text value.',
            'keys.*.key.size' => 'The OMDb API key must be exactly :size characters.',
            'keys.*.key.regex' => 'The OMDb API key may only contain digits and lowercase letters.',
            'keys.*.status.string' => 'The OMDb API key status must be a text value.',
            'keys.*.status.in' => 'The OMDb API key status must be pending, valid, invalid, or unknown.',
            'keys.*.first_seen_at.date' => 'The first seen timestamp must be a valid date.',
            'keys.*.last_checked_at.date' => 'The last checked timestamp must be a valid date.',
            'keys.*.last_confirmed_at.date' => 'The last confirmed timestamp must be a valid date.',
            'keys.*.last_response_code.integer' => 'The last response code must be a whole number.',
            'keys.*.last_response_code.between' => 'The last response code must be between :min and :max.',
        ], $request->messages());
    }

    public function test_messages_are_localized_in_other_locales(): void
    {
        app()->setLocale('es');
        $request = new ImportOmdbKeysRequest;

        $this->assertSame('Debes proporcionar al menos una entrada de clave API de OMDb.', $request->messages()['keys.required']);

        app()->setLocale('fr');
        $requestFr = new ImportOmdbKeysRequest;

        $this->assertSame('Vous devez fournir au moins une entrée de clé API OMDb.', $requestFr->messages()['keys.required']);

        app()->setLocale('en');
    }

    public function test_attributes_are_localized(): void
    {
        $request = new ImportOmdbKeysRequest;

        $this->assertSame([
            'keys' => __('validation.attributes.keys'),
            'keys.*' => __('validation.attributes.keys_entries'),
            'keys.*.key' => __('validation.attributes.keys_entry.key'),
            'keys.*.status' => __('validation.attributes.keys_entry.status'),
            'keys.*.first_seen_at' => __('validation.attributes.keys_entry.first_seen_at'),
            'keys.*.last_checked_at' => __('validation.attributes.keys_entry.last_checked_at'),
            'keys.*.last_confirmed_at' => __('validation.attributes.keys_entry.last_confirmed_at'),
            'keys.*.last_response_code' => __('validation.attributes.keys_entry.last_response_code'),
        ], $request->attributes());
    }

    public function test_keys_helper_returns_validated_payload(): void
    {
        $request = ImportOmdbKeysRequest::create('/omdb-keys/import', 'POST', [
            'keys' => [[
                'key' => 'abcd1234',
            ]],
        ]);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));

        $request->validateResolved();

        $this->assertSame([
            [
                'key' => 'abcd1234',
            ],
        ], $request->keys());
    }

    public function test_failed_validation_returns_json_payload(): void
    {
        $request = ImportOmdbKeysRequest::create('/omdb-keys/import', 'POST', []);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));

        try {
            $request->validateResolved();
            $this->fail('Expected HttpResponseException was not thrown.');
        } catch (HttpResponseException $exception) {
            $response = $exception->getResponse();

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertSame(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
            $this->assertSame([
                'message' => __('validation.custom.keys.required'),
                'errors' => [
                    'keys' => [__('validation.custom.keys.required')],
                ],
            ], $response->getData(true));
        }
    }
}
