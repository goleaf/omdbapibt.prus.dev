<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class MovieSeeder extends Seeder
{
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

        $remaining = self::TOTAL_MOVIES;

        while ($remaining > 0) {
            $batchSize = min(self::CHUNK_SIZE, $remaining);

            Movie::factory()
                ->count($batchSize)
                ->create()
                ->each(function (Movie $movie) use ($genreIds, $languageIds, $countryIds, $peopleIds, $userIds): void {
                    if ($genreIds !== []) {
                        $genreCount = min(count($genreIds), random_int(2, 4));
                        $selected = Arr::wrap(Arr::random($genreIds, $genreCount));
                        $movie->genres()->syncWithoutDetaching($selected);
                    }

                    if ($languageIds !== []) {
                        $languageCount = min(count($languageIds), random_int(1, 3));
                        $selected = Arr::wrap(Arr::random($languageIds, $languageCount));
                        $movie->languages()->syncWithoutDetaching($selected);
                    }

                    if ($countryIds !== []) {
                        $countryCount = min(count($countryIds), random_int(1, 2));
                        $selected = Arr::wrap(Arr::random($countryIds, $countryCount));
                        $movie->countries()->syncWithoutDetaching($selected);
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

                        $movie->people()->syncWithoutDetaching($pivotData);
                    }

                    if ($userIds !== []) {
                        $watchlistCount = min(count($userIds), random_int(0, 5));

                        if ($watchlistCount > 0) {
                            $selected = Arr::wrap(Arr::random($userIds, $watchlistCount));
                            $movie->watchlistedBy()->syncWithoutDetaching($selected);
                        }
                    }
                });

            $remaining -= $batchSize;
        }
    }
}
