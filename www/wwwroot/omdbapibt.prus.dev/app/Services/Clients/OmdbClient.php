<?php

namespace App\Services\Clients;

use Illuminate\Cache\CacheManager;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class OmdbClient
{
    public function __construct(
        protected HttpFactory $http,
        protected CacheManager $cache,
        protected string $apiKey,
        protected string $baseUrl = 'https://www.omdbapi.com/'
    ) {
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
        $payload = Arr::prepend($parameters, $this->apiKey, 'apikey');

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
}
