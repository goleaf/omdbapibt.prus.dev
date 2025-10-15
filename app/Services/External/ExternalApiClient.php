<?php

namespace App\Services\External;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class ExternalApiClient
{
    /**
     * @var array<string, mixed>
     */
    protected array $config;

    /**
     * @param  array<string, mixed>  $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    protected function request(array $query = []): PendingRequest
    {
        $request = Http::baseUrl($this->config['base_url'] ?? '')->acceptJson();

        if (! empty($this->config['headers'] ?? [])) {
            $request = $request->withHeaders($this->config['headers']);
        }

        if (! empty($this->config['timeout'] ?? null)) {
            $request = $request->timeout($this->config['timeout']);
        }

        $mergedQuery = array_merge($this->config['query'] ?? [], $query);

        if ($mergedQuery !== []) {
            $request = $request->withQueryParameters($mergedQuery);
        }

        return $request;
    }

    public function get(string $endpoint, array $query = []): Response
    {
        return $this->request($query)->get($endpoint);
    }

    public function post(string $endpoint, array $payload = [], array $query = []): Response
    {
        return $this->request($query)->post($endpoint, $payload);
    }

    public function put(string $endpoint, array $payload = [], array $query = []): Response
    {
        return $this->request($query)->put($endpoint, $payload);
    }

    public function delete(string $endpoint, array $query = []): Response
    {
        return $this->request($query)->delete($endpoint);
    }
}
