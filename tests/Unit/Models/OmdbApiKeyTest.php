<?php

namespace Tests\Unit\Models;

use App\Models\OmdbApiKey;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OmdbApiKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes_are_mass_assignable_and_cast(): void
    {
        $checkedAt = Carbon::create(2024, 1, 1, 12, 0, 0);
        $confirmedAt = Carbon::create(2024, 1, 2, 9, 30, 0);

        $apiKey = OmdbApiKey::factory()->create([
            'key' => 'TEST-KEY-123',
            'status' => OmdbApiKey::STATUS_VALID,
            'last_checked_at' => $checkedAt,
            'last_confirmed_at' => $confirmedAt,
        ])->fresh();

        $this->assertSame('TEST-KEY-123', $apiKey->key);
        $this->assertSame(OmdbApiKey::STATUS_VALID, $apiKey->status);
        $this->assertInstanceOf(Carbon::class, $apiKey->last_checked_at);
        $this->assertInstanceOf(Carbon::class, $apiKey->last_confirmed_at);
        $this->assertTrue($apiKey->last_checked_at->equalTo($checkedAt));
        $this->assertTrue($apiKey->last_confirmed_at->equalTo($confirmedAt));
    }

    public function test_pending_scope_filters_by_status(): void
    {
        $pending = OmdbApiKey::factory()->pending()->create();
        $withoutStatus = OmdbApiKey::factory()->withoutStatus()->create();
        OmdbApiKey::factory()->valid()->create();
        OmdbApiKey::factory()->invalid()->create();

        $results = OmdbApiKey::pending()->get();

        $this->assertTrue($results->contains($pending));
        $this->assertTrue($results->contains($withoutStatus));
        $this->assertCount(2, $results);
    }
}
