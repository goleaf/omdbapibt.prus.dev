<?php

namespace App\Services\Clients;

use Closure;
use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;

class OmdbClient
{
    protected Closure $apiKeyResolver;

    public function __construct(
        protected HttpFactory $http,
        protected CacheManager $cache,
        callable $apiKeyResolver,
        protected string $baseUrl = 'https://www.omdbapi.com/'
    ) {
        $this->apiKeyResolver = Closure::fromCallable($apiKeyResolver);
    }

    /**
     * Fetch OMDb title data by IMDb identifier with a static 24-hour cache window.
     */
    public function findByImdbId(string $imdbId, array $parameters = []): array
    {
        $parameters = Arr::prepend($parameters, $imdbId, 'i');
        $key = $this->cacheKey('title', $parameters);

        return $this->cache->remember(
            $key,
            Carbon::now()->addSeconds(config('cache_ttls.api.static')),
            fn () => $this->request($parameters)->json()
        );
    }

    /**
     * Execute an uncached OMDb request.
     */
    public function get(array $parameters): array
    {
        return $this->request($parameters)->json();
    }

    protected function request(array $parameters): Response
    {
        $this->enforceRateLimit();

        $payload = Arr::prepend($parameters, $this->resolveApiKey(), 'apikey');

        return $this->http
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->get('', $payload)
            ->throw();
    }

    protected function cacheKey(string $namespace, array $parameters): string
    {
        return sprintf('omdb.%s.%s', $namespace, md5(json_encode($parameters)));
    }

    protected function enforceRateLimit(): void
    {
        $maxPerMinute = (int) config('services.omdb.max_requests_per_minute', 60);

        if ($maxPerMinute <= 0) {
            return;
        }

        $key = 'services:omdb:global';

        if (RateLimiter::tooManyAttempts($key, $maxPerMinute)) {
            $waitSeconds = RateLimiter::availableIn($key);
            usleep(max($waitSeconds, 1) * 1_000_000);
        }

        RateLimiter::hit($key, 60);
    }

    protected function resolveApiKey(): string
    {
        $resolver = $this->apiKeyResolver;

        return (string) $resolver();
    }
}
