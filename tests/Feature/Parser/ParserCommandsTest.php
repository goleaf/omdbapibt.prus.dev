<?php

namespace Tests\Feature\Parser;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ParserCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_movies_parser_command_upserts_configured_movies(): void
    {
        config()->set('parser.movies', [
            [
                'tmdb_id' => 777,
                'imdb_id' => 'tt0000777',
                'slug' => 'parser-movie',
                'title' => 'Parser Movie',
            ],
        ]);

        Artisan::call('parser:movies');

        $this->assertDatabaseHas('movies', [
            'tmdb_id' => 777,
            'slug' => 'parser-movie',
            'title' => 'Parser Movie',
        ]);

        config()->set('parser.movies', [
            [
                'tmdb_id' => 777,
                'imdb_id' => 'tt0000777',
                'slug' => 'parser-movie',
                'title' => 'Parser Movie Updated',
            ],
        ]);

        Artisan::call('parser:movies');

        $this->assertSame(1, Movie::query()->count());
        $this->assertDatabaseHas('movies', [
            'tmdb_id' => 777,
            'title' => 'Parser Movie Updated',
        ]);
    }

    public function test_tv_parser_command_hydrates_shows_from_configuration(): void
    {
        config()->set('parser.tv_shows', [
            [
                'tmdb_id' => 555,
                'imdb_id' => 'tt0000555',
                'slug' => 'parser-show',
                'name' => 'Parser Show',
            ],
        ]);

        Artisan::call('parser:tv-shows');

        $this->assertDatabaseHas('tv_shows', [
            'tmdb_id' => 555,
            'slug' => 'parser-show',
            'name' => 'Parser Show',
        ]);

        $this->assertSame(1, TvShow::query()->count());
    }

    public function test_people_parser_command_upserts_profiles(): void
    {
        config()->set('parser.people', [
            [
                'tmdb_id' => 888,
                'imdb_id' => 'nm0000888',
                'slug' => 'parser-person',
                'name' => 'Parser Person',
            ],
        ]);

        Artisan::call('parser:people');

        $this->assertDatabaseHas('people', [
            'tmdb_id' => 888,
            'slug' => 'parser-person',
            'name' => 'Parser Person',
        ]);

        config()->set('parser.people', [
            [
                'tmdb_id' => 888,
                'imdb_id' => 'nm0000888',
                'slug' => 'parser-person',
                'name' => 'Updated Parser Person',
            ],
        ]);

        Artisan::call('parser:people');

        $this->assertSame(1, Person::query()->count());
        $this->assertDatabaseHas('people', [
            'tmdb_id' => 888,
            'name' => 'Updated Parser Person',
        ]);
    }
}
