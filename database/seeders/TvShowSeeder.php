<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\TvShow;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class TvShowSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed scripted series alongside credit and watchlist relationships.
     */
    public function run(): void
    {
        if (! Schema::hasTable('tv_shows')
            || ! Schema::hasTable('people')
            || ! Schema::hasTable('tv_show_person')) {
            return;
        }

        if (TvShow::query()->exists()) {
            return;
        }

        $personIds = Person::query()->pluck('id');
        TvShow::factory()
            ->count(1000)
            ->create()
            ->each(function (TvShow $show) use ($personIds): void {
                if ($personIds->isNotEmpty()) {
                    $creditCount = min($personIds->count(), random_int(4, 10));

                    if ($creditCount > 0) {
                        $creditPool = Collection::wrap($personIds->random($creditCount));

                        $show->people()->syncWithoutDetaching(
                            $creditPool->values()->mapWithKeys(function (int $personId, int $index): array {
                                $isCast = $index < 4;

                                return [
                                    $personId => [
                                        'credit_type' => $isCast ? 'cast' : 'crew',
                                        'department' => $isCast ? 'Acting' : fake()->randomElement(['Directing', 'Production', 'Writing']),
                                        'character' => $isCast ? fake()->name() : null,
                                        'job' => $isCast ? null : fake()->randomElement(['Showrunner', 'Producer', 'Writer']),
                                        'credit_order' => $index + 1,
                                    ],
                                ];
                            })->all()
                        );
                    }
                }

            });
    }

    private function ensureTranslationArray(?array $translations, ?string $fallback): ?array
    {
        $normalized = is_array($translations) ? $translations : [];

        if ($fallback !== null && $fallback !== '') {
            $normalized['en'] ??= $fallback;
        }

        return $normalized === [] ? null : $normalized;
    }
}
