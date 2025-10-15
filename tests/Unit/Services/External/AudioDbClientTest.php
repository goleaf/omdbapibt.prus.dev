<?php

namespace Tests\Unit\Services\External;

use App\Services\External\AudioDbClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AudioDbClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function test_it_appends_api_key_to_requests(): void
    {
        config()->set('services.audiodb', [
            'base_url' => 'https://audiodb.test/api',
            'query' => [
                'apikey' => 'audio-key',
            ],
        ]);

        $client = new AudioDbClient();

        $client->get('search.php', ['s' => 'Daft Punk']);

        Http::assertSent(function (Request $request) {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return $request->method() === 'GET'
                && str_starts_with($request->url(), 'https://audiodb.test/api/search.php')
                && ($query['apikey'] ?? null) === 'audio-key'
                && ($query['s'] ?? null) === 'Daft Punk';
        });
    }
}
