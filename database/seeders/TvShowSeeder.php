<?php

namespace Database\Seeders;

use App\Models\Person;
use App\Models\TvShow;
use App\Models\User;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class TvShowSeeder extends Seeder
{
    use SeedsModelsInChunks;

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

        $fallbackLocale = $this->fallbackLocale();

        $this->seedInChunks(self::TOTAL_SHOWS, self::CHUNK_SIZE, function (int $count) use ($peopleIds, $userIds, $fallbackLocale): void {
            TvShow::factory()
                ->count($count)
                ->create()
                ->each(function (TvShow $show) use ($peopleIds, $userIds, $fallbackLocale): void {
                    $show->forceFill([
                        'name_translations' => $this->fillTranslations(
                            $show->name_translations,
                            $this->translationFallback($show->name_translations ?? []) ?? $show->name,
                            fn (string $locale, ?string $fallback) => $locale === $fallbackLocale && $fallback !== null
                                ? $fallback
                                : $this->localizedSentence($locale)
                        ),
                        'overview_translations' => $this->fillTranslations(
                            $show->overview_translations,
                            $this->translationFallback($show->overview_translations ?? []) ?? ($show->overview ?? null),
                            fn (string $locale, ?string $fallback) => $locale === $fallbackLocale && $fallback !== null
                                ? $fallback
                                : $this->localizedParagraph($locale)
                        ),
                        'tagline_translations' => $this->fillTranslations(
                            $show->tagline_translations,
                            $this->translationFallback($show->tagline_translations ?? []) ?? ($show->tagline ?? null),
                            fn (string $locale, ?string $fallback) => $locale === $fallbackLocale && $fallback !== null
                                ? $fallback
                                : $this->localizedSentence($locale)
                        ),
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

                        $show->people()->sync($pivotData, false);
                    }

                    if ($userIds !== []) {
                        $watchlistCount = min(count($userIds), random_int(0, 5));

                        if ($watchlistCount > 0) {
                            $selected = Arr::wrap(Arr::random($userIds, $watchlistCount));
                            $show->watchlistedBy()->sync($selected, false);
                        }
                    }
                });
        });
    }
}
