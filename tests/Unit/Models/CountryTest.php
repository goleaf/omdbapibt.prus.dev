<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesLocaleTables;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use RefreshDatabase;
    use CreatesLocaleTables;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureCountriesTable();
    }

    public function test_active_flag_is_cast_to_boolean(): void
    {
        $country = Country::create([
            'name' => 'Canada',
            'code' => 'CA',
            'active' => 1,
        ]);

        $this->assertTrue($country->fresh()->active);
    }

    public function test_movies_relationship_returns_attached_movies(): void
    {
        $movie = Movie::factory()->create();
        $country = Country::create([
            'name' => 'France',
            'code' => 'FR',
            'active' => true,
        ]);

        $country->movies()->attach($movie);

        $this->assertTrue($country->movies->contains($movie));
    }
}
