<?php

namespace Tests\Feature\Parser;

use App\Models\Movie;
use App\Models\Person;
use App\Models\TvShow;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParserCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_movie_hydration_command_queues_entries(): void
    {
        $movies = Movie::factory()->count(3)->create();

        $this->artisan('parser:hydrate-movies', ['--chunk' => 2])
            ->expectsOutputToContain('Hydration dispatched for movies (2 records queued).')
            ->assertExitCode(0);

        $this->assertDatabaseCount('parser_entries', 2);

        $expectedSubjects = $movies->sortByDesc(fn ($model) => $model->getKey())->take(2);

        foreach ($expectedSubjects as $model) {
            $this->assertDatabaseHas('parser_entries', [
                'subject_type' => $model->getMorphClass(),
                'subject_id' => $model->getKey(),
                'parser' => 'movies.hydrator',
            ]);
        }
    }

    public function test_tv_hydration_uses_configured_chunk_size(): void
    {
        config(['parser.targets.tv.chunk_size' => 1]);

        $shows = TvShow::factory()->count(2)->create();

        $this->artisan('parser:hydrate-tv')
            ->expectsOutputToContain('Hydration dispatched for TV shows (1 records queued).')
            ->assertExitCode(0);

        $this->assertDatabaseCount('parser_entries', 1);
        $this->assertDatabaseHas('parser_entries', [
            'subject_type' => $shows->sortByDesc(fn ($model) => $model->getKey())->first()->getMorphClass(),
            'subject_id' => $shows->sortByDesc(fn ($model) => $model->getKey())->first()->getKey(),
            'parser' => 'tv.hydrator',
        ]);
    }

    public function test_people_hydration_respects_since_option(): void
    {
        $stale = Person::factory()->create(['updated_at' => now()->subDays(5)]);
        $fresh = Person::factory()->create(['updated_at' => now()->subHours(2)]);

        $this->artisan('parser:hydrate-people', ['--since' => now()->subDay()->toIso8601String()])
            ->expectsOutputToContain('Hydration dispatched for people (1 records queued).')
            ->assertExitCode(0);

        $this->assertDatabaseHas('parser_entries', [
            'subject_type' => $fresh->getMorphClass(),
            'subject_id' => $fresh->getKey(),
            'parser' => 'people.hydrator',
        ]);

        $this->assertDatabaseMissing('parser_entries', [
            'subject_id' => $stale->getKey(),
            'parser' => 'people.hydrator',
        ]);
    }
}
