<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reviews\ReviewForm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReviewFormValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_review_form_requires_all_fields(): void
    {
        $user = \App\Models\User::factory()->create();

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->set('form.movieTitle', '')
            ->set('form.rating', null)
            ->set('form.body', '')
            ->call('submit')
            ->assertHasErrors([
                'form.movieTitle' => ['required'],
                'form.rating' => ['required'],
                'form.body' => ['required'],
            ])
            ->assertSee(__('reviews.validation.movie_title.required'))
            ->assertSee(__('reviews.validation.rating.required'))
            ->assertSee(__('reviews.validation.body.required'));
    }

    public function test_validation_messages_are_localized(): void
    {
        $user = \App\Models\User::factory()->create();

        $originalLocale = app()->getLocale();

        app()->setLocale('es');

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->set('form.movieTitle', str_repeat('A', 260))
            ->set('form.rating', 6)
            ->set('form.body', '')
            ->call('submit')
            ->assertHasErrors([
                'form.movieTitle' => ['max'],
                'form.rating' => ['between'],
                'form.body' => ['required'],
            ])
            ->assertSee(__('reviews.validation.movie_title.max', ['max' => 255]))
            ->assertSee(__('reviews.validation.rating.between', ['min' => 1, 'max' => 5]))
            ->assertSee(__('reviews.validation.body.required'));

        app()->setLocale('fr');

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->set('form.movieTitle', '')
            ->set('form.rating', 4)
            ->set('form.body', str_repeat('A', 2100))
            ->call('submit')
            ->assertHasErrors([
                'form.movieTitle' => ['required'],
                'form.body' => ['max'],
            ])
            ->assertSee(__('reviews.validation.movie_title.required'))
            ->assertSee(__('reviews.validation.body.max', ['max' => 2000]));

        app()->setLocale($originalLocale);
    }

    public function test_success_message_is_translated_after_submission(): void
    {
        $user = \App\Models\User::factory()->create();

        $originalLocale = app()->getLocale();

        app()->setLocale('fr');

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->set('form.movieTitle', 'Film Exemple')
            ->set('form.rating', 5)
            ->set('form.body', 'Une critique brève.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', __('reviews.messages.submitted'))
            ->assertSee(__('reviews.messages.submitted'));

        app()->setLocale($originalLocale);
    }
}
