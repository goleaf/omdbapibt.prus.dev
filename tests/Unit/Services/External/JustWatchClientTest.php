<?php

namespace Tests\Unit\Services\External;

use App\Services\External\JustWatchClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class JustWatchClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Http::fake();
    }

    public function test_it_sets_authorization_header_when_configured(): void
    {
        config()->set('services.justwatch', [
            'base_url' => 'https://justwatch.test',
            'headers' => [
                'Authorization' => 'Bearer justwatch-token',
            ],
        ]);

        $client = new JustWatchClient;

        $client->post('titles', ['query' => 'test']);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'POST'
                && str_starts_with($request->url(), 'https://justwatch.test/titles')
                && $request->header('Authorization')[0] === 'Bearer justwatch-token';
        });
    }
}
