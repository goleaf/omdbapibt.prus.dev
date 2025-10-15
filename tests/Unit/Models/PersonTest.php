<?php

namespace Tests\Unit\Models;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonTest extends TestCase
{
    use RefreshDatabase;

    public function test_casts_dates_and_translation_payload(): void
    {
        $person = Person::factory()->create([
            'birthday' => '1980-06-01',
            'deathday' => '2020-01-01',
            'biography_translations' => [
                'en' => 'English bio',
                'es' => 'Biografía en español',
            ],
        ]);

        $this->assertSame('1980-06-01', $person->birthday->toDateString());
        $this->assertSame('2020-01-01', $person->deathday->toDateString());
        $this->assertSame('English bio', $person->biography_translations['en']);
    }

    public function test_movies_and_tv_shows_relationships(): void
    {
        $movie = Movie::factory()->create();
        $show = TvShow::factory()->create();
        $person = Person::factory()->create();

        $person->movies()->attach($movie->id, ['credit_type' => 'cast']);
        $person->tvShows()->attach($show->id, ['credit_type' => 'crew']);

        $person->load('movies', 'tvShows');

        $this->assertTrue($person->movies->contains($movie));
        $this->assertTrue($person->tvShows->contains($show));
    }
}
