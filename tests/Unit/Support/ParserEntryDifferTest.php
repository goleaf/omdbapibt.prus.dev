<?php

namespace Tests\Unit\Support;

use App\Support\ParserEntryDiffer;
use PHPUnit\Framework\TestCase;

class ParserEntryDifferTest extends TestCase
{
    public function test_diff_highlights_changes_between_payloads(): void
    {
        $differ = new ParserEntryDiffer;

        $baseline = [
            'title' => 'Original Title',
            'overview' => [
                'en' => 'Existing overview',
            ],
            'genres' => ['Action'],
            'runtime' => 120,
        ];

        $incoming = [
            'title' => 'New Title',
            'overview' => [
                'en' => 'Existing overview',
                'es' => 'Descripción en español',
            ],
            'genres' => ['Action', 'Drama'],
            'runtime' => 125,
            'popularity' => 42.5,
        ];

        $diff = $differ->diff($baseline, $incoming);

        $this->assertContainsEquals([
            'key' => 'title',
            'before' => 'Original Title',
            'after' => 'New Title',
        ], $diff);

        $this->assertContainsEquals([
            'key' => 'overview.es',
            'before' => null,
            'after' => 'Descripción en español',
        ], $diff);

        $this->assertContainsEquals([
            'key' => 'genres.1',
            'before' => null,
            'after' => 'Drama',
        ], $diff);

        $this->assertContainsEquals([
            'key' => 'runtime',
            'before' => 120,
            'after' => 125,
        ], $diff);

        $this->assertContainsEquals([
            'key' => 'popularity',
            'before' => null,
            'after' => 42.5,
        ], $diff);

        $this->assertCount(5, $diff);
    }

    public function test_diff_returns_empty_array_when_payloads_match(): void
    {
        $differ = new ParserEntryDiffer;

        $baseline = [
            'title' => 'Same',
            'overview' => ['en' => 'No change'],
        ];

        $incoming = [
            'title' => 'Same',
            'overview' => ['en' => 'No change'],
        ];

        $diff = $differ->diff($baseline, $incoming);

        $this->assertSame([], $diff);
    }
}
