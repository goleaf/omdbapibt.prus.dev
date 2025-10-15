<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Http\Requests\Api\MovieLookupRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MovieLookupRequestTest extends TestCase
{
    public function test_it_allows_any_user(): void
    {
        $request = new MovieLookupRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_it_defines_expected_rules(): void
    {
        $request = new MovieLookupRequest;

        $this->assertSame([
            'query' => ['required', 'string', 'min:2'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ], $request->rules());
    }

    public function test_it_accepts_valid_payloads(): void
    {
        $request = MovieLookupRequest::create('/api/movies/lookup', 'GET', [
            'query' => 'Star Wars',
            'limit' => 25,
        ]);

        $validator = Validator::make($request->all(), $request->rules());

        $this->assertTrue($validator->passes());
        $this->assertSame([
            'query' => 'Star Wars',
            'limit' => 25,
        ], $validator->validated());
    }

    public function test_it_returns_localized_messages(): void
    {
        app()->setLocale('en');

        $request = new MovieLookupRequest;

        $this->assertSame([
            'query.required' => 'Please enter a search query.',
            'query.string' => 'The search query must be a text value.',
            'query.min' => 'The search query must be at least :min characters.',
            'limit.integer' => 'The result limit must be a whole number.',
            'limit.min' => 'The result limit must be at least :min.',
            'limit.max' => 'The result limit may not be greater than :max.',
        ], $request->messages());
    }

    public function test_it_localizes_messages_for_other_locales(): void
    {
        app()->setLocale('fr');

        $request = new MovieLookupRequest;

        $this->assertSame([
            'query.required' => 'Veuillez fournir un terme de recherche.',
            'query.string' => 'Le terme de recherche doit être une chaîne de caractères.',
            'query.min' => 'Le terme de recherche doit contenir au moins :min caractères.',
            'limit.integer' => 'La limite de résultats doit être un nombre entier.',
            'limit.min' => 'La limite de résultats doit être au moins de :min.',
            'limit.max' => 'La limite de résultats ne peut pas dépasser :max.',
        ], $request->messages());
    }

    public function test_it_exposes_localized_attribute_names(): void
    {
        app()->setLocale('es');

        $request = new MovieLookupRequest;

        $this->assertSame([
            'query' => 'término de búsqueda',
            'limit' => 'límite de resultados',
        ], $request->attributes());
    }

    public function test_failed_validation_returns_json_error_payload(): void
    {
        app()->setLocale('en');

        $request = MovieLookupRequest::create('/api/movies/lookup', 'GET', []);
        $request->setContainer(app());
        $request->setRedirector(app('redirect'));

        try {
            $request->validateResolved();
            $this->fail('An HttpResponseException was not thrown.');
        } catch (HttpResponseException $exception) {
            $response = $exception->getResponse();

            $this->assertInstanceOf(JsonResponse::class, $response);
            $this->assertSame(JsonResponse::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
            $this->assertSame([
                'message' => __('validation.custom.query.required'),
                'errors' => [
                    'query' => [__('validation.custom.query.required')],
                ],
            ], $response->getData(true));
        }
    }
}
