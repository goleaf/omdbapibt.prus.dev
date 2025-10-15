<?php

namespace Tests\Unit\Http\Requests\Api;

use App\Http\Requests\Api\MovieLookupRequest;
use Tests\TestCase;

class MovieLookupRequestTest extends TestCase
{
    public function test_authorization_allows_public_access(): void
    {
        $request = new MovieLookupRequest;

        $this->assertTrue($request->authorize());
    }

    public function test_rules_match_expected_configuration(): void
    {
        $request = new MovieLookupRequest;

        $this->assertSame([
            'query' => ['required', 'string', 'min:2'],
            'limit' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ], $request->rules());
    }

    public function test_messages_use_localized_translation_keys(): void
    {
        app()->setLocale('fr');

        $request = new MovieLookupRequest;

        $this->assertSame([
            'query.required' => __('validation.movie_lookup.query.required'),
            'query.string' => __('validation.movie_lookup.query.string'),
            'query.min' => __('validation.movie_lookup.query.min'),
            'limit.integer' => __('validation.movie_lookup.limit.integer'),
            'limit.min' => __('validation.movie_lookup.limit.min'),
            'limit.max' => __('validation.movie_lookup.limit.max'),
        ], $request->messages());
    }
}
