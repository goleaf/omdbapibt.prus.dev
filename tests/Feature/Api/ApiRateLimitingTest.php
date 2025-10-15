<?php

namespace Tests\Feature\Api;

use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_movie_endpoint_enforces_rate_limiting(): void
    {
        $movie = Movie::factory()->create([
            'slug' => 'limit-test-movie',
            'title' => 'Limit Test Movie',
        ]);

        $ipAddress = '203.0.113.10';
        RateLimiter::clear($this->limiterCacheKey('public-api', $ipAddress));

        $limit = (int) config('services.rate_limits.public_api', 60);

        for ($i = 0; $i < $limit; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                ->getJson("/api/v1/movies/{$movie->slug}")
                ->assertOk();
        }

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->getJson("/api/v1/movies/{$movie->slug}")
            ->assertStatus(429);
    }

    public function test_parser_dispatch_requires_basic_authentication(): void
    {
        $ipAddress = '203.0.113.20';
        RateLimiter::clear($this->limiterCacheKey('parser-trigger', 'ip:'.$ipAddress));

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->postJson('/api/v1/parser/dispatch', ['workload' => 'movies'])
            ->assertStatus(401);
    }

    public function test_parser_dispatch_rejects_non_admin_users(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $ipAddress = '203.0.113.30';
        RateLimiter::clear($this->limiterCacheKey('parser-trigger', 'user:'.$user->getAuthIdentifier()));

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->withHeaders([
                'PHP_AUTH_USER' => $user->email,
                'PHP_AUTH_PW' => 'password',
            ])
            ->postJson('/api/v1/parser/dispatch', ['workload' => 'movies'])
            ->assertStatus(403);
    }

    public function test_parser_dispatch_endpoint_is_throttled_for_admins(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $ipAddress = '203.0.113.40';
        RateLimiter::clear($this->limiterCacheKey('parser-trigger', 'user:'.$user->getAuthIdentifier()));

        Queue::fake();

        $limit = (int) config('services.rate_limits.parser_trigger', 5);

        for ($i = 0; $i < $limit; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
                ->withHeaders([
                    'PHP_AUTH_USER' => $user->email,
                    'PHP_AUTH_PW' => 'password',
                ])
                ->postJson('/api/v1/parser/dispatch', ['workload' => 'movies'])
                ->assertStatus(202);
        }

        Queue::assertPushed(ExecuteParserPipeline::class, $limit);

        $this->withServerVariables(['REMOTE_ADDR' => $ipAddress])
            ->withHeaders([
                'PHP_AUTH_USER' => $user->email,
                'PHP_AUTH_PW' => 'password',
            ])
            ->postJson('/api/v1/parser/dispatch', ['workload' => 'movies'])
            ->assertStatus(429);
    }

    private function limiterCacheKey(string $name, string $key): string
    {
        return md5($name.$key);
    }
}
