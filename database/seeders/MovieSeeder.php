<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\Person;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class MovieSeeder extends Seeder
{
    /**
     * Seed feature films with related catalogue data.
     */
    public function run(): void
    {
        if (Movie::query()->exists()) {
            return;
        }

        $genres = Genre::query()->get();
        $languages = Language::query()->get();
        $countries = Country::query()->get();
        $people = Person::query()->get();
        $users = User::query()->get();

        Movie::factory()
            ->count(24)
            ->create()
            ->each(function (Movie $movie) use ($genres, $languages, $countries, $people, $users): void {
                if ($genres->isNotEmpty()) {
                    $genreCount = min($genres->count(), random_int(2, 4));
                    $genreIds = Collection::wrap($genres->random($genreCount))->pluck('id')->all();
                    $movie->genres()->syncWithoutDetaching($genreIds);
                }

                if ($languages->isNotEmpty()) {
                    $languageCount = min($languages->count(), random_int(1, 3));
                    $languageIds = Collection::wrap($languages->random($languageCount))->pluck('id')->all();
                    $movie->languages()->syncWithoutDetaching($languageIds);
                }

                if ($countries->isNotEmpty()) {
                    $countryCount = min($countries->count(), random_int(1, 2));
                    $countryIds = Collection::wrap($countries->random($countryCount))->pluck('id')->all();
                    $movie->countries()->syncWithoutDetaching($countryIds);
                }

                if ($people->isNotEmpty()) {
                    $creditPool = Collection::wrap($people->random(min($people->count(), random_int(4, 8))));

                    $movie->people()->syncWithoutDetaching(
                        $creditPool->values()->mapWithKeys(function (Person $person, int $index): array {
                            $isCast = $index < 3;

                            return [
                                $person->getKey() => [
                                    'credit_type' => $isCast ? 'cast' : 'crew',
                                    'department' => $isCast ? 'Acting' : fake()->randomElement(['Directing', 'Production', 'Writing']),
                                    'character' => $isCast ? fake()->name() : null,
                                    'job' => $isCast ? null : fake()->randomElement(['Director', 'Producer', 'Writer']),
                                    'credit_order' => $index + 1,
                                ],
                            ];
                        })->all()
                    );
                }

                if ($users->isNotEmpty()) {
                    $watchlistCount = min($users->count(), random_int(0, 5));

                    if ($watchlistCount > 0) {
                        $userIds = Collection::wrap($users->random($watchlistCount))->pluck('id')->all();
                        $movie->watchlistedBy()->syncWithoutDetaching($userIds);
                    }
                }
            });
    }
}
