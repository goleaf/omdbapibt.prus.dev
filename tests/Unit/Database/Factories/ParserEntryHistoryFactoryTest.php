<?php

namespace Tests\Unit\Database\Factories;

use App\Enums\ParserReviewAction;
use App\Models\ParserEntryHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParserEntryHistoryFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_history_with_related_models(): void
    {
        $history = ParserEntryHistory::factory()->create();

        $this->assertNotNull($history->entry);
        $this->assertNotNull($history->user);
        $this->assertInstanceOf(ParserReviewAction::class, $history->action);
        $this->assertIsArray($history->changes);
        $this->assertNotEmpty($history->changes);
    }
}
