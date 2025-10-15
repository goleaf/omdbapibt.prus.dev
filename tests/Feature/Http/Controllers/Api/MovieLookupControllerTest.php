<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MovieLookupControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_matching_movies(): void
    {
        $older = Movie::factory()->create([
            'title' => [
                'en' => 'Flux Runner',
                'es' => 'Flux Runner (ES)',
                'fr' => 'Flux Runner (FR)',
            ],
            'original_title' => 'Flux Runner',
            'imdb_id' => 'tt1111111',
            'tmdb_id' => 111111,
            'slug' => 'flux-runner',
            'year' => 2021,
            'updated_at' => now()->subDay(),
        ]);

        $newer = Movie::factory()->create([
            'title' => [
                'en' => 'Flux Legacy',
                'es' => 'Flux Legacy (ES)',
                'fr' => 'Flux Legacy (FR)',
            ],
            'original_title' => 'Flux Legacy',
            'imdb_id' => 'tt2222222',
            'tmdb_id' => 222222,
            'slug' => 'flux-legacy',
            'year' => 2024,
            'updated_at' => now(),
        ]);

        Movie::factory()->create([
            'title' => [
                'en' => 'Orbit Drift',
                'es' => 'Orbit Drift (ES)',
                'fr' => 'Orbit Drift (FR)',
            ],
            'original_title' => 'Orbit Drift',
            'imdb_id' => 'tt3333333',
            'tmdb_id' => 333333,
            'slug' => 'orbit-drift',
            'year' => 2019,
        ]);

        $response = $this->getJson(route('api.movies.lookup', ['query' => 'Flux']));

        $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->has('data', 2)
            ->where('data.0.id', $newer->id)
            ->where('data.0.title', $newer->title)
            ->where('data.0.original_title', 'Flux Legacy')
            ->where('data.0.imdb_id', 'tt2222222')
            ->where('data.0.tmdb_id', 222222)
            ->where('data.0.slug', 'flux-legacy')
            ->where('data.0.year', 2024)
            ->where('data.1.id', $older->id)
            ->where('data.1.title', $older->title)
            ->where('data.1.original_title', 'Flux Runner')
            ->where('data.1.imdb_id', 'tt1111111')
            ->where('data.1.tmdb_id', 111111)
            ->where('data.1.slug', 'flux-runner')
            ->where('data.1.year', 2021)
        );
    }

    public function test_it_localizes_validation_errors_in_spanish(): void
    {
        app()->setLocale('es');

        $response = $this->getJson(route('api.movies.lookup'));

        $response
            ->assertUnprocessable()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('message', 'Por favor ingresa un término de búsqueda.')
                ->where('errors.query.0', 'Por favor ingresa un término de búsqueda.')
            );
    }

    public function test_it_localizes_validation_errors_in_french(): void
    {
        app()->setLocale('fr');

        $response = $this->getJson(route('api.movies.lookup', [
            'query' => 'Flux',
            'limit' => 0,
        ]));

        $response
            ->assertUnprocessable()
            ->assertJson(fn (AssertableJson $json) => $json
                ->where('message', 'La limite de résultats doit être au moins de 1.')
                ->where('errors.limit.0', 'La limite de résultats doit être au moins de 1.')
            );
    }
}
