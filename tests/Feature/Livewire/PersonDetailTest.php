<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\PersonDetail;
use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class PersonDetailTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::get('/login', fn () => 'login')->name('login');
        Route::get('/register', fn () => 'register')->name('register');
    }

    public function test_renders_person_detail_with_translations_and_grouped_credits(): void
    {
        $person = Person::factory()->create([
            'name' => 'Luna Star',
            'slug' => 'luna-star',
            'biography' => [
                'en' => 'English biography content.',
                'es' => 'Biografia en espanol.',
            ],
            'birthday' => '1985-05-05',
            'known_for_department' => 'Acting',
            'popularity' => 123.4,
        ]);

        $movie = Movie::factory()->create([
            'title' => [
                'en' => 'Galactic Tale',
            ],
            'slug' => 'galactic-tale',
            'release_date' => '2020-01-01',
        ]);

        $show = TvShow::factory()->create([
            'name' => 'Nebula Series',
            'slug' => 'nebula-series',
            'first_air_date' => '2018-09-01',
        ]);

        $person->movieCredits()->attach($movie->id, [
            'role' => 'acting',
            'character' => 'Captain Nova',
            'credit_order' => 1,
        ]);

        $person->tvCredits()->attach($show->id, [
            'role' => 'crew',
            'job' => 'Director',
            'department' => 'Directing',
            'credit_order' => 1,
        ]);

        Livewire::test(PersonDetail::class, ['identifier' => $person->slug])
            ->assertSee('Luna Star')
            ->assertSee('English biography content.')
            ->assertSee('Biografia en espanol.')
            ->assertSee('Acting')
            ->assertSee('Crew')
            ->assertSee('Captain Nova')
            ->assertSee('Director')
            ->assertSee('Nebula Series')
            ->assertSee('profile-avatar.svg');
    }

    public function test_person_detail_route_resolves_by_id(): void
    {
        $person = Person::factory()->create([
            'name' => 'Orion Skye',
        ]);

        $this->get(route('people.show', $person->id))
            ->assertOk()
            ->assertSeeLivewire(PersonDetail::class)
            ->assertSee('Orion Skye');
    }
}
