<?php

namespace Tests\Feature\Livewire;

use App\Livewire\PersonDetail;
use App\Models\Movie;
use App\Models\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PersonDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_person_detail_renders_by_slug(): void
    {
        $person = Person::factory()->create([
            'biography_translations' => ['en' => 'Explorer of cosmic cinema.'],
        ]);

        $movie = Movie::factory()->create();
        $movie->people()->attach($person->id, [
            'credit_type' => 'cast',
            'character' => 'Navigator',
        ]);

        Livewire::test(PersonDetail::class, ['person' => $person->slug])
            ->assertOk()
            ->assertSee($person->name)
            ->assertSee('Navigator')
            ->assertSee('Explorer of cosmic cinema.');
    }

    public function test_person_detail_renders_by_id(): void
    {
        $person = Person::factory()->create();

        Livewire::test(PersonDetail::class, ['person' => (string) $person->id])
            ->assertOk()
            ->assertSee($person->name);
    }

    public function test_person_detail_uses_locale_biography_translation(): void
    {
        app()->setLocale('es');

        $person = Person::factory()->create([
            'biography_translations' => [
                'es' => 'Cronista estelar de la era digital.',
                'en' => 'Digital era star chronicler.',
            ],
        ]);

        Livewire::test(PersonDetail::class, ['person' => $person->slug])
            ->assertSee('Cronista estelar de la era digital.')
            ->assertDontSee('Digital era star chronicler.');
    }

    public function test_person_detail_falls_back_to_default_biography_when_missing_translations(): void
    {
        app()->setLocale('fr');

        $person = Person::factory()->create([
            'biography' => 'Biographie hors-ligne.',
            'biography_translations' => null,
        ]);

        Livewire::test(PersonDetail::class, ['person' => $person->slug])
            ->assertSee('Biographie hors-ligne.');
    }

    public function test_person_detail_displays_localized_empty_biography_message(): void
    {
        app()->setLocale('fr');

        $person = Person::factory()->create([
            'biography' => null,
            'biography_translations' => [],
        ]);

        Livewire::test(PersonDetail::class, ['person' => $person->slug])
            ->assertSee(__('ui.people.no_biography'));
    }
}
