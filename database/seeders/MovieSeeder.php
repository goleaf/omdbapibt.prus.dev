<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
use App\Models\User;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class MovieSeeder extends Seeder
{
    use SeedsModelsInChunks;

    private const TOTAL_MOVIES = 1000;

    private const CHUNK_SIZE = 100;

    /**
     * Seed feature films with related catalogue data.
     */
    public function run(): void
    {
        if (Movie::query()->exists()) {
            return;
        }

        $genreIds = Genre::query()->pluck('id')->all();
        $languageIds = Language::query()->pluck('id')->all();
        $countryIds = Country::query()->pluck('id')->all();
        $peopleIds = Person::query()->pluck('id')->all();
        $userIds = User::query()->pluck('id')->all();

        $fallbackLocale = $this->fallbackLocale();

        $this->seedInChunks(self::TOTAL_MOVIES, self::CHUNK_SIZE, function (int $count) use ($genreIds, $languageIds, $countryIds, $peopleIds, $userIds, $fallbackLocale): void {
            Movie::factory()
                ->count($count)
                ->create()
                ->each(function (Movie $movie) use ($genreIds, $languageIds, $countryIds, $peopleIds, $userIds, $fallbackLocale): void {
                    $movie->forceFill([
                        'title' => $this->fillTranslations(
                            is_array($movie->title) ? $movie->title : [],
                            $this->translationFallback($movie->title),
                            fn (string $locale, ?string $fallback) => $locale === $fallbackLocale && $fallback !== null
                                ? $fallback
                                : $this->localizedSentence($locale)
                        ),
                        'overview' => $this->fillTranslations(
                            is_array($movie->overview) ? $movie->overview : [],
                            $this->translationFallback($movie->overview),
                            fn (string $locale, ?string $fallback) => $locale === $fallbackLocale && $fallback !== null
                                ? $fallback
                                : $this->localizedParagraph($locale)
                        ),
                    ])->saveQuietly();

                    if ($genreIds !== []) {
                        $genreCount = min(count($genreIds), random_int(2, 4));
                        $selected = Arr::wrap(Arr::random($genreIds, $genreCount));
                        $movie->genres()->sync($selected, false);
                    }

                    if ($languageIds !== []) {
                        $languageCount = min(count($languageIds), random_int(1, 3));
                        $selected = Arr::wrap(Arr::random($languageIds, $languageCount));
                        $movie->languages()->sync($selected, false);
                    }

                    if ($countryIds !== []) {
                        $countryCount = min(count($countryIds), random_int(1, 2));
                        $selected = Arr::wrap(Arr::random($countryIds, $countryCount));
                        $movie->countries()->sync($selected, false);
                    }

                    if ($peopleIds !== []) {
                        $creditCount = min(count($peopleIds), random_int(4, 8));
                        $creditIds = array_values(Arr::wrap(Arr::random($peopleIds, $creditCount)));
                        $pivotData = [];

                        foreach ($creditIds as $index => $personId) {
                            $isCast = $index < 3;

                            $pivotData[$personId] = [
                                'credit_type' => $isCast ? 'cast' : 'crew',
                                'department' => $isCast ? 'Acting' : Arr::random(['Directing', 'Production', 'Writing']),
                                'character' => $isCast ? fake()->name() : null,
                                'job' => $isCast ? null : Arr::random(['Director', 'Producer', 'Writer']),
                                'credit_order' => $index + 1,
                            ];
                        }

                        $movie->people()->sync($pivotData, false);
                    }

                    if ($userIds !== []) {
                        $watchlistCount = min(count($userIds), random_int(0, 5));

                        if ($watchlistCount > 0) {
                            $selected = Arr::wrap(Arr::random($userIds, $watchlistCount));
                            $movie->watchlistedBy()->sync($selected, false);
                        }
                    }
                });
        });
    }
}
