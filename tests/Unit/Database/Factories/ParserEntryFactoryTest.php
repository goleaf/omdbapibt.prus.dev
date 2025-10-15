<?php

namespace Tests\Unit\Database\Factories;

use App\Enums\ParserEntryStatus;
use App\Models\Movie;
use App\Models\ParserEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParserEntryFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_state_creates_pending_movie_entry(): void
    {
        $entry = ParserEntry::factory()->create();

        $this->assertSame(ParserEntryStatus::Pending, $entry->status);
        $this->assertInstanceOf(Movie::class, $entry->subject);
        $this->assertIsArray($entry->payload);
        $this->assertArrayHasKey('title', $entry->payload);
    }

    public function test_status_helpers_override_status(): void
    {
        $approved = ParserEntry::factory()->approved()->create();
        $rejected = ParserEntry::factory()->rejected()->create();

        $this->assertSame(ParserEntryStatus::Approved, $approved->status);
        $this->assertSame(ParserEntryStatus::Rejected, $rejected->status);
    }
}
