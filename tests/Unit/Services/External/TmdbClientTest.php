<?php

namespace Tests\Unit\Services\External;

use App\Services\External\TmdbClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TmdbClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function test_it_appends_api_key_to_requests(): void
    {
        config()->set('services.tmdb', [
            'base_url' => 'https://example.test/api',
            'query' => [
                'api_key' => 'tmdb-key',
            ],
        ]);

        $client = new TmdbClient;

        $client->get('movie/123', ['language' => 'en-US']);

        Http::assertSent(function (Request $request) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return $request->method() === 'GET'
                && str_starts_with($request->url(), 'https://example.test/api/movie/123')
                && ($query['api_key'] ?? null) === 'tmdb-key'
                && ($query['language'] ?? null) === 'en-US';
        });
    }
}
