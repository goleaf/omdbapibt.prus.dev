<?php

namespace Tests\Unit\Models;

use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_sanitized_body_removes_disallowed_markup(): void
    {
        $user = User::factory()->create();

        $review = Review::factory()->create([
            'user_id' => $user->id,
            'body' => '<p>Hello World</p><script>alert(1)</script><strong>Enjoy!</strong>',
        ]);

        $sanitized = $review->sanitized_body;

        $this->assertStringContainsString('<p>Hello World</p>', $sanitized);
        $this->assertStringContainsString('<strong>Enjoy!</strong>', $sanitized);
        $this->assertStringNotContainsString('<script>', $sanitized);
    }

    public function test_user_relationship_is_eager_loaded(): void
    {
        $review = Review::factory()->create();
        $reloaded = Review::find($review->id);

        $this->assertTrue($reloaded->relationLoaded('user'));
        $this->assertInstanceOf(User::class, $reloaded->user);
    }
}
