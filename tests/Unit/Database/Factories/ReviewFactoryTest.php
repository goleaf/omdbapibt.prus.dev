<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_factory_creates_review_with_user(): void
    {
        $review = Review::factory()->create();

        $this->assertNotNull($review->user);
        $this->assertGreaterThanOrEqual(1, $review->rating);
        $this->assertLessThanOrEqual(5, $review->rating);
        $this->assertStringContainsString('<p>', $review->body);
    }
}
