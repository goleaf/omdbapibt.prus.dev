<?php

namespace Tests\Unit\Services\Clients;

use App\Services\Clients\OmdbClient;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory;
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
        $http = new Factory();
        $http->fake([
            'https://omdb.test/*' => $http->response(['Title' => 'Example'], 200),
        ]);

        $cache = new CacheManager(app());
        $client = new OmdbClient($http, $cache, 'test-key', 'https://omdb.test/');

        $client->findByImdbId('tt1234567');
        $client->findByImdbId('tt1234567');

        $http->assertSentCount(1);

        Carbon::setTestNow(now()->addDay()->addSecond());
        $client->findByImdbId('tt1234567');

        $http->assertSentCount(2);
    }
}
