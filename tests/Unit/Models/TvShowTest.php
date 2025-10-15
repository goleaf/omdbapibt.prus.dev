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
}
