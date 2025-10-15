<?php

namespace Tests\Unit\Database\Factories;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_person_factory_produces_translated_biography(): void
    {
        $person = Person::factory()->create();

        $this->assertIsArray($person->biography_translations);
        $this->assertArrayHasKey('en', $person->biography_translations);
        $this->assertNotEmpty($person->slug);
        $this->assertNotNull($person->birthday);
    }
}
