<?php

namespace App\Services;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;

class OmdbClient
{
    private const BASE_URL = 'https://www.omdbapi.com/';

    public function __construct(private readonly HttpFactory $http)
    {
    }

    /**
     * Perform a GET request against the OMDb API with global throttling.
     *
     * @param  array<string, string>  $query
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    public function get(array $query): array
    {
        $this->enforceRateLimit();

        $key = config('services.omdb.key');

        if (empty($key)) {
            throw new RuntimeException('The OMDb API key is not configured.');
        }

        $response = $this->pendingRequest()
            ->retry(2, 500)
            ->get('', array_merge(['apikey' => $key], $query));

        $response->throw();

        return $response->json();
    }

    private function pendingRequest(): PendingRequest
    {
        return $this->http->baseUrl(self::BASE_URL)
            ->acceptJson()
            ->asJson();
    }

    private function enforceRateLimit(): void
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
}
