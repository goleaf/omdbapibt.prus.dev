<?php

namespace Tests\Unit\Models;

use App\Models\OmdbApiKeyProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\CarbonImmutable;
use Tests\TestCase;

class OmdbApiKeyProgressTest extends TestCase
{
    use RefreshDatabase;

    public function test_fillable_attributes_are_mass_assignable_and_cast(): void
    {
        $progress = OmdbApiKeyProgress::factory()->create([
            'sequence_cursor' => 12345,
        ])->fresh();

        $this->assertSame('12345', $progress->sequence_cursor);
        $this->assertInstanceOf(CarbonImmutable::class, $progress->created_at);
        $this->assertInstanceOf(CarbonImmutable::class, $progress->updated_at);
    }

    public function test_latest_checkpoint_scope_orders_by_recent_updates(): void
    {
        $older = OmdbApiKeyProgress::factory()->create([
            'sequence_cursor' => 'first',
        ]);
        $older->forceFill(['updated_at' => CarbonImmutable::create(2024, 1, 1, 12, 0, 0)])->save();

        $newer = OmdbApiKeyProgress::factory()->create([
            'sequence_cursor' => 'second',
        ]);
        $newer->forceFill(['updated_at' => CarbonImmutable::create(2024, 1, 1, 15, 30, 0)])->save();

        $latest = OmdbApiKeyProgress::latestCheckpoint()->first();

        $this->assertTrue($latest->is($newer));
    }
}
