<?php

namespace Tests\Feature\Livewire;

use App\Livewire\MovieDetail;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class MovieDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Route::middleware('web')->group(function (): void {
            Route::get('/login', fn () => 'login')->name('login');
            Route::get('/register', fn () => 'register')->name('register');
            Route::post('/logout', fn () => 'logout')->name('logout');
        });

        Route::getRoutes()->refreshNameLookups();

        $this->assertTrue(Route::has('login'));
        $this->assertTrue(Route::has('logout'));
    }

    public function test_movie_detail_page_renders_via_slug(): void
    {
        $movie = Movie::factory()->create();

        $response = $this->get(route('movies.show', ['movie' => $movie->slug]));

        $response
            ->assertOk()
            ->assertSeeLivewire(MovieDetail::class)
            ->assertSee($movie->title['en'], false)
            ->assertSee('Top billed cast', false);
    }

    public function test_movie_detail_route_accepts_id_binding(): void
    {
        $movie = Movie::factory()->create();

        $response = $this->get(route('movies.show', ['movie' => $movie->id]));

        $response
            ->assertOk()
            ->assertSee($movie->title['en'], false);
    }

    public function test_movie_detail_component_exposes_streaming_and_translations(): void
    {
        $movie = Movie::factory()->create();

        $streamingService = $movie->streaming_links[0]['service'] ?? null;
        $firstLocaleKey = array_key_first($movie->translations);

        $this->assertNotNull($streamingService);
        $this->assertNotNull($firstLocaleKey);

        $firstLocale = strtoupper((string) $firstLocaleKey);

        Livewire::test(MovieDetail::class, ['movie' => $movie])
            ->call('setTab', 'streaming')
            ->assertSee($streamingService, false)
            ->call('setTab', 'translations')
            ->assertSee($firstLocale, false);
    }
}
