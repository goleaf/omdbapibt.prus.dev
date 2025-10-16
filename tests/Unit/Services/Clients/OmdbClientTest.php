<?php

namespace Tests\Unit\Services\Clients;

use App\Services\Clients\OmdbClient;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OmdbClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');
        Carbon::setTestNow(now());
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_static_lookup_is_cached_for_twenty_four_hours(): void
    {
        $http = new Factory;
        $http->fake([
            'https://omdb.test/*' => $http->response(['Title' => 'Example'], 200),
        ]);

        $cache = new CacheManager(app());
        $client = new OmdbClient($http, $cache, fn () => 'test-key', 'https://omdb.test/');

        $client->findByImdbId('tt1234567');
        $client->findByImdbId('tt1234567');

        $http->assertSentCount(1);

        Carbon::setTestNow(now()->addDay()->addSecond());
        $client->findByImdbId('tt1234567');

        $http->assertSentCount(2);
    }

    public function test_it_resolves_api_key_for_each_request(): void
    {
        $http = new Factory;
        $captured = [];

        $http->fake([
            'https://omdb.test/*' => function (Request $request) use (&$captured, $http) {
                $captured[] = $request;

                return $http->response(['Title' => 'Example'], 200);
            },
        ]);

        $cache = new CacheManager(app());
        $keys = ['first-key', 'second-key'];
        $client = new OmdbClient(
            $http,
            $cache,
            function () use (&$keys) {
                return array_shift($keys) ?? 'fallback-key';
            },
            'https://omdb.test/'
        );

        $client->get(['t' => 'Inception']);
        $client->get(['t' => 'Interstellar']);

        $this->assertCount(2, $captured);

        $firstQuery = [];
        parse_str((string) parse_url($captured[0]->url(), PHP_URL_QUERY), $firstQuery);
        $secondQuery = [];
        parse_str((string) parse_url($captured[1]->url(), PHP_URL_QUERY), $secondQuery);

        $this->assertSame('first-key', $firstQuery['apikey'] ?? null);
        $this->assertSame('second-key', $secondQuery['apikey'] ?? null);
    }
}
