<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\Person;
use App\Models\Review;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_multilingual_records(): void
    {
        $this->seed();

        $locales = config('translatable.locales', ['en']);

        $this->assertDatabaseCount('users', 1000);
        $this->assertDatabaseCount('people', 1000);
        $this->assertDatabaseCount('movies', 1000);
        $this->assertDatabaseCount('tv_shows', 1000);
        $this->assertDatabaseCount('reviews', 1000);

        foreach ($locales as $locale) {
            $this->assertTrue(
                User::query()->where('preferred_locale', $locale)->exists(),
                sprintf('Expected at least one user with preferred locale [%s]', $locale)
            );

            $this->assertSame(1000, Movie::query()->whereNotNull("title->{$locale}")->count());
            $this->assertSame(1000, Movie::query()->whereNotNull("overview->{$locale}")->count());

            $this->assertSame(1000, TvShow::query()->whereNotNull("name_translations->{$locale}")->count());
            $this->assertSame(1000, TvShow::query()->whereNotNull("overview_translations->{$locale}")->count());
            $this->assertSame(1000, TvShow::query()->whereNotNull("tagline_translations->{$locale}")->count());

            $this->assertSame(1000, Person::query()->whereNotNull("biography_translations->{$locale}")->count());

            $this->assertSame(1000, Review::query()->where('body', 'like', sprintf('%%lang="%s"%%', $locale))->count());
        }
    }
}
