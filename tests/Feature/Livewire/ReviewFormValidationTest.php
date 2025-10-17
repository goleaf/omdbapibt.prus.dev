<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reviews\ReviewForm;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ReviewFormValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_validation_messages_are_localized(): void
    {
        $expectations = [
            'en' => [
                'movie' => 'Please select a movie.',
                'rating' => 'Choose a rating between 1 and 5.',
                'body' => 'Please share your review.',
            ],
            'es' => [
                'movie' => 'Por favor selecciona una película.',
                'rating' => 'Elige una calificación entre 1 y 5.',
                'body' => 'Por favor comparte tu reseña.',
            ],
            'fr' => [
                'movie' => 'Veuillez sélectionner un film.',
                'rating' => 'Choisissez une note entre 1 et 5.',
                'body' => 'Veuillez partager votre avis.',
            ],
        ];

        foreach ($expectations as $locale => $messages) {
            app()->setLocale($locale);

            Livewire::test(ReviewForm::class)
                ->set('form.movieId', '')
                ->set('form.rating', 0)
                ->set('form.body', '')
                ->call('submit')
                ->assertHasErrors([
                    'form.movieId' => ['required'],
                    'form.rating' => ['between'],
                    'form.body' => ['required'],
                ])
                ->assertSee($messages['movie'])
                ->assertSee($messages['rating'])
                ->assertSee($messages['body']);
        }

        app()->setLocale(config('app.locale'));
    }

    public function test_success_message_is_translated_after_submission(): void
    {
        $user = \App\Models\User::factory()->create();

        app()->setLocale('fr');

        $movie = Movie::factory()->create();

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->set('form.movieId', $movie->id)
            ->set('form.rating', 5)
            ->set('form.body', 'Un classique moderne.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', __('reviews.status.submitted'))
            ->assertSee(__('reviews.status.submitted'));
    }
}
