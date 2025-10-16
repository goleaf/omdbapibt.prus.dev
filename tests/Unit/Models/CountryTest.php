<?php

namespace Tests\Unit\Models;

use App\Models\Country;
use App\Models\Movie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesLocaleTables;
use Tests\TestCase;

class CountryTest extends TestCase
{
    use CreatesLocaleTables;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureCountriesTable();
    }

    public function test_active_flag_is_cast_to_boolean(): void
    {
        $country = Country::create([
            'name' => 'Canada',
            'name_translations' => ['en' => 'Canada', 'fr' => 'Canada'],
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
            'name_translations' => ['en' => 'France', 'fr' => 'France'],
            'code' => 'FR',
            'active' => true,
        ]);

        $country->movies()->attach($movie);

        $this->assertTrue($country->movies->contains($movie));
    }

    public function test_localized_name_returns_translation(): void
    {
        $country = Country::create([
            'name' => 'Mexico',
            'name_translations' => ['en' => 'Mexico', 'es' => 'México'],
            'code' => 'MX',
            'active' => true,
        ]);

        $this->assertSame('México', $country->localizedName('es'));
    }
}
