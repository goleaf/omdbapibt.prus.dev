<?php

namespace Tests\Feature\Database;

use App\Enums\UserRole;
use App\Models\Movie;
use App\Models\Person;
use App\Models\Review;
use App\Models\TvShow;
use App\Models\UiTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainSeederVolumeTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_localized_domain_records(): void
    {
        $this->seed();

        $this->assertSame(1000, User::query()->count());
        $this->assertGreaterThanOrEqual(2, User::query()->where('role', UserRole::Admin->value)->count());
        $this->assertSame(1000, Person::query()->count());
        $this->assertSame(1000, Movie::query()->count());
        $this->assertSame(1000, TvShow::query()->count());
        $this->assertSame(1000, UiTranslation::query()->count());
        $this->assertSame(1000, Review::query()->count());

        $movie = Movie::query()->firstOrFail();
        $this->assertIsArray($movie->title);
        $this->assertArrayHasKey('en', $movie->title);
        $this->assertArrayHasKey('es', $movie->title);

        $person = Person::query()->firstOrFail();
        $this->assertIsArray($person->biography_translations);
        $this->assertArrayHasKey('fr', $person->biography_translations);

        $translation = UiTranslation::query()->firstOrFail();
        $translationValues = $translation->getTranslations('value');
        $this->assertArrayHasKey('fr', $translationValues);

        $review = Review::query()->firstOrFail();
        $this->assertNotEmpty($review->movie_title);
        $this->assertIsString($review->body);
    }
}
