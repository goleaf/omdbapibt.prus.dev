<?php

namespace Tests\Feature\Parser;

use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonMassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_person_can_mass_assign_translation_and_poster_attributes(): void
    {
        $payload = Person::factory()->raw([
            'biography_translations' => [
                'en' => 'Test biography in English.',
                'es' => 'Biografía de prueba en español.',
            ],
            'poster_path' => '/test/poster.jpg',
        ]);

        $person = Person::create($payload);

        $this->assertSame('Test biography in English.', $person->biography_translations['en']);
        $this->assertSame('Biografía de prueba en español.', $person->biography_translations['es']);
        $this->assertSame('/test/poster.jpg', $person->poster_path);
    }
}
