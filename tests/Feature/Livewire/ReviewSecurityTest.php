<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reviews\ReviewForm;
use App\Livewire\Reviews\ReviewList;
use App\Models\Movie;
use App\Models\Review;
use App\Support\HtmlSanitizer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReviewSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_form_renders_csrf_token(): void
    {
        $user = \App\Models\User::factory()->create();

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->assertSeeHtml('name="_token"');
    }

    public function test_review_submission_sanitizes_malicious_html(): void
    {
        $user = \App\Models\User::factory()->create();
        $movie = Movie::factory()->create();

        $malicious = '<script>alert(1)</script><p><strong>Great</strong> movie!</p>';

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->set('form.movieId', $movie->id)
            ->set('form.rating', 4)
            ->set('form.body', $malicious)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', __('reviews.status.submitted'));

        $review = Review::first();

        $this->assertNotNull($review);
        $this->assertSame($movie->id, $review->movie_id);
        $this->assertStringNotContainsString('<script>', $review->body);
        $this->assertSame(HtmlSanitizer::clean($malicious), $review->body);
        $this->assertStringContainsString('<strong>Great</strong> movie!', $review->body);
    }

    public function test_review_list_only_renders_sanitized_html(): void
    {
        $review = Review::factory()->create([
            'body' => '<script>alert(1)</script><p>Allowed <strong>formatting</strong></p>',
        ]);

        Livewire::test(ReviewList::class)
            ->assertDontSeeHtml('<script>alert(1)</script>')
            ->assertSeeHtml('<strong>formatting</strong>');
    }
}
