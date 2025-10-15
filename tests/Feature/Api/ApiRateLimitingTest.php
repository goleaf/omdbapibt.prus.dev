<?php

namespace Tests\Feature\Api;

use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ApiRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_movie_lookup_rate_limiter_configuration_is_respected(): void
    {
        config([
            'rate-limiting.movie_lookup.max_attempts' => 2,
            'rate-limiting.movie_lookup.decay_seconds' => 60,
        ]);

        Movie::factory()->count(3)->create([
            'title' => 'Lookup Sample',
        ]);

        $this->assertSame(2, config('rate-limiting.movie_lookup.max_attempts'));

        $this->getJson('/api/movies/lookup?query=Lookup')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json->has('data')->etc());

        $this->getJson('/api/movies/lookup?query=Lookup')->assertOk();

        $this->getJson('/api/movies/lookup?query=Lookup')->assertStatus(429);
    }

    public function test_parser_trigger_enforces_basic_auth_before_throttling(): void
    {
        config([
            'parser.queue' => 'priority-parsing',
            'rate-limiting.parser_trigger.max_attempts' => 2,
            'rate-limiting.parser_trigger.decay_seconds' => 60,
        ]);

        Queue::fake();

        $route = Route::getRoutes()->getByName('api.parser.trigger');
        $middlewareStack = $route->gatherMiddleware();

        $this->assertSame('api', $middlewareStack[0] ?? null);

        $throttleIndex = $this->findMiddlewareIndex($middlewareStack, ['throttle:parser-trigger']);
        $this->assertGreaterThan(0, $throttleIndex);

        $apiGroup = app('router')->getMiddlewareGroups()['api'] ?? [];
        $basicIndex = $this->findMiddlewareIndex($apiGroup, [AuthenticateWithBasicAuth::class, 'auth.basic']);

        $this->assertSame(0, $basicIndex, 'Basic authentication should be the first middleware in the API group.');

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->postJson('/api/parser/trigger', ['workload' => 'movies'])
                ->assertStatus(401);
        }

        $user = User::factory()->admin()->create();
        $authHeader = 'Basic '.base64_encode($user->email.':password');

        $this->withHeader('Authorization', $authHeader)
            ->postJson('/api/parser/trigger', ['workload' => 'movies'])
            ->assertStatus(202)
            ->assertJson([
                'status' => 'queued',
                'workload' => 'movies',
                'queue' => 'priority-parsing',
            ]);

        Queue::assertPushed(ExecuteParserPipeline::class, function (ExecuteParserPipeline $job): bool {
            return $job->workload === 'movies' && $job->queue === 'priority-parsing';
        });

        $this->withHeader('Authorization', $authHeader)
            ->postJson('/api/parser/trigger', ['workload' => 'movies'])
            ->assertStatus(202);

        $this->withHeader('Authorization', $authHeader)
            ->postJson('/api/parser/trigger', ['workload' => 'movies'])
            ->assertStatus(429);
    }

    /**
     * @param  array<int, string>  $candidates
     */
    protected function findMiddlewareIndex(array $stack, array $candidates): int
    {
        foreach ($candidates as $candidate) {
            $index = array_search($candidate, $stack, true);

            if ($index !== false) {
                return $index;
            }
        }

        $this->fail('Failed to locate expected middleware: '.implode(', ', $candidates));

        return -1;
    }
}
