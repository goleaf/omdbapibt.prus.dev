<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ApiRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_api_requests_are_throttled_after_limit_is_exceeded(): void
    {
        RateLimiter::clear('public-api|127.0.0.1');

        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/status')->assertOk();
        }

        $response = $this->getJson('/api/status');

        $response->assertStatus(429);
        $response->assertJson([
            'message' => 'Too many requests. Please slow down.',
        ]);
    }

    public function test_parser_trigger_requests_use_stricter_rate_limit(): void
    {
        $user = User::factory()->create();

        RateLimiter::clear('parser-triggers|'.$user->getAuthIdentifier());

        $this->actingAs($user);

        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/parsers/run')->assertStatus(202);
        }

        $response = $this->postJson('/api/parsers/run');

        $response->assertStatus(429);
        $response->assertJson([
            'message' => 'Too many parsing requests. Please wait before retrying.',
        ]);
    }
}
