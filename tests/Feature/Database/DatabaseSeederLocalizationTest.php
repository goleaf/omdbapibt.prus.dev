<?php

namespace Tests\Feature\Database;

use App\Models\Movie;
use App\Models\Person;
use App\Models\Review;
use App\Models\TvShow;
use App\Models\UiTranslation;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_large_multilingual_datasets(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertSame(1_000, User::query()->count());
        $this->assertSame(1_000, Person::query()->count());
        $this->assertSame(1_000, Movie::query()->count());
        $this->assertSame(1_000, TvShow::query()->count());
        $this->assertSame(1_000, UiTranslation::query()->count());
        $this->assertSame(1_000, Review::query()->count());

        $movie = Movie::query()->firstOrFail();

        $this->assertIsArray($movie->title);
        $this->assertArrayHasKey('en', $movie->title);
        $this->assertArrayHasKey('es', $movie->title);
        $this->assertArrayHasKey('fr', $movie->title);

        $translations = UiTranslation::query()->firstOrFail()->getTranslations('value');

        $this->assertArrayHasKey('en', $translations);
        $this->assertArrayHasKey('es', $translations);
        $this->assertArrayHasKey('fr', $translations);

        $this->assertGreaterThanOrEqual(2, User::query()->where('role', 'admin')->count());
    }
}
