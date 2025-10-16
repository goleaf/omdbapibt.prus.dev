<?php

namespace Tests\Unit\Models;

use App\Models\Person;
use App\Models\TvShow;
use App\Models\User;
use App\Models\WatchHistory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TvShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_casts_dates_and_flags(): void
    {
        $show = TvShow::factory()->create([
            'adult' => true,
            'first_air_date' => '2020-01-01',
            'last_air_date' => '2021-12-31',
            'popularity' => 12.345,
            'vote_average' => 7.8,
        ]);

        $show->refresh();

        $this->assertTrue($show->adult);
        $this->assertSame('2020-01-01', $show->first_air_date->toDateString());
        $this->assertSame('2021-12-31', $show->last_air_date->toDateString());
        $this->assertSame(12.345, $show->popularity);
        $this->assertSame(7.8, $show->vote_average);
    }

    public function test_localized_accessors_return_expected_values(): void
    {
        $show = TvShow::factory()->create([
            'name' => 'Default Name',
            'name_translations' => [
                'en' => 'Default Name',
                'es' => 'Nombre Predeterminado',
                'fr' => 'Nom Par Défaut',
            ],
            'overview' => 'Base overview',
            'overview_translations' => [
                'fr' => 'Vue d\'ensemble française',
            ],
            'tagline' => 'Base tagline',
            'tagline_translations' => [
                'es' => 'Eslogan en español',
            ],
        ]);

        app()->setLocale('es');
        $this->assertSame('Nombre Predeterminado', $show->localizedName());
        $this->assertSame('Base overview', $show->localizedOverview());
        $this->assertSame('Eslogan en español', $show->localizedTagline());

        app()->setLocale('fr');
        $this->assertSame('Nom Par Défaut', $show->localizedName());
        $this->assertSame('Vue d\'ensemble française', $show->localizedOverview());
        $this->assertSame('Base tagline', $show->localizedTagline());

        app()->setLocale('de');
        $this->assertSame('Default Name', $show->localizedName());
        $this->assertSame('Base overview', $show->localizedOverview());
        $this->assertSame('Base tagline', $show->localizedTagline());
    }

    public function test_watchlist_and_history_relationships(): void
    {
        $show = TvShow::factory()->create();
        $user = User::factory()->create();

        $show->watchlistedBy()->attach($user);

        $history = WatchHistory::factory()
            ->forTvShow($show)
            ->create([
                'user_id' => $user->id,
            ]);

        $show->load('watchlistedBy', 'watchHistories');

        $this->assertTrue($show->watchlistedBy->contains($user));
        $this->assertTrue($show->watchHistories->contains($history));
    }

    public function test_people_relationship_returns_attached_people(): void
    {
        $show = TvShow::factory()->create();
        $person = Person::factory()->create();

        $show->people()->attach($person->id, [
            'credit_type' => 'cast',
            'department' => 'Acting',
            'character' => 'Lead',
            'job' => null,
            'credit_order' => 1,
        ]);

        $show->load('people');

        $pivot = $show->people->first()->pivot;

        $this->assertSame('Lead', $pivot->character);
        $this->assertSame('Acting', $pivot->department);
        $this->assertSame(1, $pivot->credit_order);
    }

    public function test_localized_accessors_and_scope(): void
    {
        $match = TvShow::factory()->create([
            'name' => 'Example Show',
            'name_translations' => [
                'en' => 'Example Show',
                'es' => 'Programa de Ejemplo',
            ],
            'overview' => 'English overview',
            'overview_translations' => [
                'en' => 'English overview',
                'es' => 'Descripción en español',
            ],
            'tagline' => 'English tagline',
            'tagline_translations' => [
                'en' => 'English tagline',
                'es' => 'Lema en español',
            ],
        ]);

        $nonMatch = TvShow::factory()->create([
            'name' => 'Another Show',
            'name_translations' => [
                'en' => 'Another Show',
                'es' => 'Serie Alternativa',
            ],
        ]);

        $this->assertSame('Programa de Ejemplo', $match->localizedName('es'));
        $this->assertSame('Example Show', $match->localizedName('fr'));
        $this->assertSame('Descripción en español', $match->localizedOverview('es'));
        $this->assertSame('English overview', $match->localizedOverview('fr'));
        $this->assertSame('Lema en español', $match->localizedTagline('es'));

        $results = TvShow::query()->whereLocalizedNameLike('Programa', 'es')->get();

        $this->assertTrue($results->contains($match));
        $this->assertFalse($results->contains($nonMatch));
    }
}
