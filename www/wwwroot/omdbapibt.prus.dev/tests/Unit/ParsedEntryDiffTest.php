<?php

namespace Tests\Unit;

use App\Models\ParsedEntry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParsedEntryDiffTest extends TestCase
{
    use RefreshDatabase;

    public function test_diff_detects_added_updated_and_removed_fields(): void
    {
        $entry = ParsedEntry::factory()->create([
            'original_payload' => [
                'title' => 'Original Title',
                'year' => 1999,
                'metadata' => ['runtime' => 120],
            ],
            'parsed_payload' => [
                'title' => 'Updated Title',
                'year' => 1999,
                'metadata' => ['runtime' => 125],
                'tagline' => 'Brand new',
            ],
        ]);

        $diff = $entry->diff();

        $this->assertArrayHasKey('added', $diff);
        $this->assertArrayHasKey('updated', $diff);
        $this->assertArrayHasKey('removed', $diff);
        $this->assertSame(['tagline' => 'Brand new'], $diff['added']);
        $this->assertSame([
            'title' => ['from' => 'Original Title', 'to' => 'Updated Title'],
            'metadata.runtime' => ['from' => 120, 'to' => 125],
        ], $diff['updated']);
        $this->assertSame([], $diff['removed']);
    }
}
