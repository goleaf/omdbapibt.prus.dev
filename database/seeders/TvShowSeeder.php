<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\TvShow;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TvShowSeeder extends Seeder
{
    private const TOTAL_SHOWS = 1000;

    private const CHUNK_SIZE = 100;

    /**
     * Seed scripted series alongside credit and watchlist relationships.
     */
    public function run(): void
    {
        if (TvShow::query()->exists()) {
            return;
        }

        $peopleIds = Person::query()->pluck('id')->all();
        $userIds = User::query()->pluck('id')->all();

        $remaining = self::TOTAL_SHOWS;

        while ($remaining > 0) {
            $batchSize = min(self::CHUNK_SIZE, $remaining);

            TvShow::factory()
                ->count($batchSize)
                ->create()
                ->each(function (TvShow $show) use ($peopleIds, $userIds): void {
                    $show->forceFill([
                        'name_translations' => $this->ensureTranslationArray($show->name_translations, $show->name),
                        'overview_translations' => $this->ensureTranslationArray($show->overview_translations, $show->overview),
                        'tagline_translations' => $this->ensureTranslationArray($show->tagline_translations, $show->tagline),
                    ])->saveQuietly();

                    if ($peopleIds !== []) {
                        $creditCount = min(count($peopleIds), random_int(4, 10));
                        $creditIds = array_values(Arr::wrap(Arr::random($peopleIds, $creditCount)));
                        $pivotData = [];

                        foreach ($creditIds as $index => $personId) {
                            $isCast = $index < 4;

                            $pivotData[$personId] = [
                                'credit_type' => $isCast ? 'cast' : 'crew',
                                'department' => $isCast ? 'Acting' : Arr::random(['Directing', 'Production', 'Writing']),
                                'character' => $isCast ? fake()->name() : null,
                                'job' => $isCast ? null : Arr::random(['Showrunner', 'Producer', 'Writer']),
                                'credit_order' => $index + 1,
                            ];
                        }

                        $show->people()->syncWithoutDetaching($pivotData);
                    }

                    if ($userIds !== []) {
                        $watchlistCount = min(count($userIds), random_int(0, 5));

                        if ($watchlistCount > 0) {
                            $selected = Arr::wrap(Arr::random($userIds, $watchlistCount));
                            $show->watchlistedBy()->syncWithoutDetaching($selected);
                        }
                    }
                });

            $remaining -= $batchSize;
        }
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
