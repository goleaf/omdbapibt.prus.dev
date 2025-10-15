<?php

namespace Tests\Unit\Services\External;

use App\Services\External\OmdbClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OmdbClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function test_it_appends_api_key_to_requests(): void
    {
        config()->set('services.omdb', [
            'base_url' => 'https://omdb.test',
            'query' => [
                'apikey' => 'omdb-key',
            ],
        ]);

        $client = new OmdbClient();

        $client->get('/', ['t' => 'Inception']);

        Http::assertSent(function (Request $request) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return $request->method() === 'GET'
                && str_starts_with($request->url(), 'https://omdb.test/')
                && ($query['apikey'] ?? null) === 'omdb-key'
                && ($query['t'] ?? null) === 'Inception';
        });
    }
}
