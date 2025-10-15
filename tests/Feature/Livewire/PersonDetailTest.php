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
            ->assertSee('Navigator');
    }

    public function test_person_detail_renders_by_id(): void
    {
        $person = Person::factory()->create();

        Livewire::test(PersonDetail::class, ['person' => (string) $person->id])
            ->assertOk()
            ->assertSee($person->name);
    }
}
