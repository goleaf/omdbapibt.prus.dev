<?php

namespace App\Services\Clients;

use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TmdbClient
{
    public function __construct(
        protected HttpFactory $http,
        protected CacheManager $cache,
        protected string $apiKey,
        protected string $baseUrl = 'https://api.themoviedb.org/3/'
    ) {
    }

    /**
     * Fetch static TMDb resources (genres, configuration, etc.) with a 24-hour cache window.
     */
    public function getStatic(string $endpoint, array $parameters = []): array
    {
        $key = $this->cacheKey('static', $endpoint, $parameters);

        return $this->cache->remember(
            $key,
            Carbon::now()->addSeconds(config('cache_ttls.api.static')),
            fn () => $this->request($endpoint, $parameters)->json()
        );
    }

    /**
     * Fetch trending resources and cache the response for one hour using tags for targeted invalidation.
     */
    public function getTrending(string $mediaType, string $timeWindow = 'day', array $parameters = []): array
    {
        $endpoint = sprintf('trending/%s/%s', $mediaType, $timeWindow);
        $key = $this->cacheKey('trending', $endpoint, $parameters);

        return $this->cache
            ->tags(['tmdb', 'trending'])
            ->remember(
                $key,
                Carbon::now()->addSeconds(config('cache_ttls.api.trending')),
                fn () => $this->request($endpoint, $parameters)->json()
            );
    }

    /**
     * Fetch popular resources and cache the response for one hour using tags for targeted invalidation.
     */
    public function getPopular(string $mediaType, array $parameters = []): array
    {
        $endpoint = sprintf('%s/popular', $mediaType);
        $key = $this->cacheKey('popular', $endpoint, $parameters);

        return $this->cache
            ->tags(['tmdb', 'popular'])
            ->remember(
                $key,
                Carbon::now()->addSeconds(config('cache_ttls.api.popular')),
                fn () => $this->request($endpoint, $parameters)->json()
            );
    }

    /**
     * Clear the cached responses for trending requests.
     */
    public function clearTrendingCache(): void
    {
        $this->cache->tags(['tmdb', 'trending'])->flush();
    }

    /**
     * Clear the cached responses for popular requests.
     */
    public function clearPopularCache(): void
    {
        $this->cache->tags(['tmdb', 'popular'])->flush();
    }

    /**
     * Execute a raw TMDb request without caching.
     */
    public function get(string $endpoint, array $parameters = []): array
    {
        return $this->request($endpoint, $parameters)->json();
    }

    protected function request(string $endpoint, array $parameters = []): Response
    {
        $payload = Arr::prepend($parameters, $this->apiKey, 'api_key');

        return $this->http
            ->baseUrl($this->baseUrl)
            ->acceptJson()
            ->get($endpoint, $payload)
            ->throw();
    }

    protected function cacheKey(string $namespace, string $endpoint, array $parameters = []): string
    {
        return sprintf(
            'tmdb.%s.%s.%s',
            $namespace,
            trim($endpoint, '/'),
            md5(json_encode($parameters))
        );
    }
}
