<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Reviews\ReviewForm;
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
                'movie' => 'Please enter the movie title.',
                'rating' => 'Choose a rating between 1 and 5.',
                'body' => 'Please share your review.',
            ],
            'es' => [
                'movie' => 'Por favor escribe el título de la película.',
                'rating' => 'Elige una calificación entre 1 y 5.',
                'body' => 'Por favor comparte tu reseña.',
            ],
            'fr' => [
                'movie' => 'Veuillez saisir le titre du film.',
                'rating' => 'Choisissez une note entre 1 et 5.',
                'body' => 'Veuillez partager votre avis.',
            ],
        ];

        foreach ($expectations as $locale => $messages) {
            app()->setLocale($locale);

            Livewire::test(ReviewForm::class)
                ->set('form.movieTitle', '')
                ->set('form.rating', 0)
                ->set('form.body', '')
                ->call('submit')
                ->assertHasErrors([
                    'form.movieTitle' => ['required'],
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

        Livewire::actingAs($user)
            ->test(ReviewForm::class)
            ->set('form.movieTitle', 'Le Fabuleux Destin d\'Amélie Poulain')
            ->set('form.rating', 5)
            ->set('form.body', 'Un classique moderne.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', __('reviews.status.submitted'))
            ->assertSee(__('reviews.status.submitted'));
    }
}
