<?php

namespace Tests\Feature\Api;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MovieLookupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_lookup_returns_matching_movies(): void
    {
        $matching = Movie::factory()->create([
            'title' => [
                'en' => 'Lookup Match',
                'es' => 'Coincidencia de búsqueda',
                'fr' => 'Correspondance de recherche',
            ],
            'original_title' => 'Lookup Match',
            'imdb_id' => 'tt1234567',
            'tmdb_id' => 1234567,
            'slug' => 'lookup-match',
            'year' => 2024,
        ]);

        Movie::factory()->create([
            'title' => [
                'en' => 'Different Movie',
                'es' => 'Película diferente',
                'fr' => 'Film différent',
            ],
        ]);

        $this->getJson('/api/movies/lookup?query=Lookup')
            ->assertOk()
            ->assertJson(fn (AssertableJson $json) => $json
                ->has('data', 1)
                ->where('data.0.id', $matching->id)
                ->where('data.0.title', $matching->title)
                ->where('data.0.original_title', $matching->original_title)
                ->where('data.0.imdb_id', $matching->imdb_id)
                ->where('data.0.tmdb_id', $matching->tmdb_id)
                ->where('data.0.slug', $matching->slug)
                ->where('data.0.year', $matching->year)
                ->etc()
            );
    }

    public function test_lookup_validation_errors_are_localized(): void
    {
        app()->setLocale('es');

        $response = $this->getJson('/api/movies/lookup');

        $response
            ->assertStatus(422)
            ->assertJson([
                'message' => __('validation.movie_lookup.query.required'),
            ])
            ->assertJsonValidationErrors([
                'query' => __('validation.movie_lookup.query.required'),
            ]);
    }
}
