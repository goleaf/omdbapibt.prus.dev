<?php

namespace Tests\Unit\Services\Clients;

use App\Services\Clients\TmdbClient;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TmdbClientTest extends TestCase
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

    public function test_static_requests_are_cached_for_twenty_four_hours(): void
    {
        $client = $this->makeClient(
            ['https://example.com/genre/list*' => fn (Factory $http) => $http->response(['genres' => [1]], 200)]
        );

        $client->getStatic('genre/list');
        $client->getStatic('genre/list');

        $client->http()->assertSentCount(1);

        Carbon::setTestNow(now()->addDay()->addSecond());
        $client->getStatic('genre/list');

        $client->http()->assertSentCount(2);
    }

    public function test_trending_requests_use_tagged_cache_for_one_hour(): void
    {
        $client = $this->makeClient(
            ['https://example.com/trending/movie/day*' => fn (Factory $http) => $http->response(['results' => [1]], 200)]
        );

        $client->getTrending('movie');
        $client->getTrending('movie');

        $client->http()->assertSentCount(1);

        Carbon::setTestNow(now()->addHour()->addSecond());
        $client->getTrending('movie');

        $client->http()->assertSentCount(2);
    }

    public function test_clearing_trending_tags_forces_subsequent_requests(): void
    {
        $client = $this->makeClient(
            ['https://example.com/trending/movie/day*' => fn (Factory $http) => $http->response(['results' => [1]], 200)]
        );

        $client->getTrending('movie');
        $client->clearTrendingCache();
        $client->getTrending('movie');

        $client->http()->assertSentCount(2);
    }

    public function test_popular_requests_use_tagged_cache_for_one_hour(): void
    {
        $client = $this->makeClient(
            ['https://example.com/movie/popular*' => fn (Factory $http) => $http->response(['results' => [1]], 200)]
        );

        $client->getPopular('movie');
        $client->getPopular('movie');

        $client->http()->assertSentCount(1);

        Carbon::setTestNow(now()->addHour()->addSecond());
        $client->getPopular('movie');

        $client->http()->assertSentCount(2);
    }

    public function test_clearing_popular_tags_forces_subsequent_requests(): void
    {
        $client = $this->makeClient(
            ['https://example.com/movie/popular*' => fn (Factory $http) => $http->response(['results' => [1]], 200)]
        );

        $client->getPopular('movie');
        $client->clearPopularCache();
        $client->getPopular('movie');

        $client->http()->assertSentCount(2);
    }

    protected function makeClient(array $fakes): object
    {
        $http = new Factory;

        $patterns = [];
        foreach ($fakes as $pattern => $callback) {
            $patterns[$pattern] = $callback($http);
        }
        $http->fake($patterns);

        $cache = new CacheManager(app());

        $client = new class($http, $cache) extends TmdbClient
        {
            public function __construct(Factory $http, CacheManager $cache)
            {
                parent::__construct($http, $cache, 'test-key', 'https://example.com/');
                $this->http = $http;
            }

            public function http(): Factory
            {
                return $this->http;
            }
        };

        return $client;
    }
}
