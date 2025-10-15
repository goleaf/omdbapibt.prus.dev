<?php

namespace Tests\Feature\Livewire;

use App\Livewire\People\PersonDetail;
use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PersonDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_displays_person_details_and_translations(): void
    {
        app()->setLocale('en');

        $person = Person::factory()->create([
            'biography' => [
                'en' => 'English biography text.',
                'es' => 'Biografía en español.',
            ],
            'gender' => 1,
            'known_for_department' => 'Acting',
            'also_known_as' => ['Alias One', 'Alias Two'],
            'place_of_birth' => 'Test City',
            'birthday' => '1980-05-10',
            'popularity' => 9.123,
            'homepage' => 'https://example.com',
        ]);

        $movieCast = Movie::factory()->create([
            'title' => 'Example Movie',
            'release_date' => '2020-01-01',
        ]);

        $movieCrew = Movie::factory()->create([
            'title' => 'Crew Movie',
            'release_date' => '2022-03-01',
        ]);

        $tvShow = TvShow::factory()->create([
            'name' => 'Example Series',
            'first_air_date' => '2021-04-01',
        ]);

        $person->movieCredits()->attach($movieCast->id, [
            'role' => 'cast',
            'character' => 'Hero Name',
            'order' => 1,
        ]);

        $person->movieCredits()->attach($movieCrew->id, [
            'role' => 'crew',
            'job' => 'Director',
            'order' => 2,
        ]);

        $person->tvCredits()->attach($tvShow->id, [
            'role' => 'cast',
            'character' => 'Lead Character',
            'order' => 1,
        ]);

        Livewire::test(PersonDetail::class, ['person' => $person])
            ->assertSet('person.id', $person->id)
            ->assertSee($person->name)
            ->assertSee('English biography text.')
            ->assertSee('Biografía en español.')
            ->assertSee('Personal Details')
            ->assertSee('Acting')
            ->assertSee('Alias One, Alias Two')
            ->assertSee('as Hero Name')
            ->assertSee('(2020)')
            ->assertSee('Director')
            ->assertSee('Example Series')
            ->assertSee('Lead Character')
            ->assertSee('(2021)');
    }

    public function test_it_404s_for_unknown_person(): void
    {
        $this->get(route('people.show', ['person' => 'unknown-person']))
            ->assertNotFound();
    }

    public function test_route_binds_person_by_slug(): void
    {
        $person = Person::factory()->create([
            'name' => 'Bound Person',
        ]);

        $this->get(route('people.show', $person))
            ->assertOk()
            ->assertSee('Bound Person');
    }

    public function test_route_binds_person_by_numeric_identifier(): void
    {
        $person = Person::factory()->create([
            'name' => 'Numeric Person',
        ]);

        $this->get(route('people.show', ['person' => $person->getKey()]))
            ->assertOk()
            ->assertSee('Numeric Person');
    }
}
