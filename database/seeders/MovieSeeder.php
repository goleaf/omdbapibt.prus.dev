<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\ListModel;
use App\Models\Movie;
use App\Models\Person;
use App\Models\User;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MovieSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed feature films with related catalogue data.
     */
    public function run(): void
    {
        if (! Schema::hasTable('movies')
            || ! Schema::hasTable('genres')
            || ! Schema::hasTable('languages')
            || ! Schema::hasTable('countries')
            || ! Schema::hasTable('people')
            || ! Schema::hasTable('users')
            || ! Schema::hasTable('movie_genre')
            || ! Schema::hasTable('movie_language')
            || ! Schema::hasTable('movie_country')
            || ! Schema::hasTable('movie_person')
            || ! Schema::hasTable('lists')
            || ! Schema::hasTable('list_items')) {
            return;
        }

        if (Movie::query()->exists()) {
            return;
        }

        $genreIds = Genre::query()->pluck('id');
        $languageIds = Language::query()->pluck('id');
        $countryIds = Country::query()->pluck('id');
        $personIds = Person::query()->pluck('id');
        $users = User::query()->get();

        $watchLaterLists = $users->mapWithKeys(function (User $user): array {
            $list = ListModel::firstOrCreate(
                [
                    'user_id' => $user->getKey(),
                    'title' => ListModel::WATCH_LATER_TITLE,
                ],
                [
                    'public' => false,
                    'description' => null,
                    'cover_url' => null,
                ],
            );

            return [$user->getKey() => $list];
        });

        $this->forChunkedCount(1_000, 100, function (int $count) use ($genreIds, $languageIds, $countryIds, $personIds, $watchLaterLists): void {
            Movie::factory()
                ->count($count)
                ->create()
                ->each(function (Movie $movie) use ($genreIds, $languageIds, $countryIds, $personIds, $watchLaterLists): void {
                    if ($genreIds->isNotEmpty()) {
                        $genreCount = min($genreIds->count(), random_int(2, 4));
                        $selectedGenres = collect($genreIds->random($genreCount))->values()->all();
                        $movie->genres()->syncWithoutDetaching($selectedGenres);
                    }

                    if ($languageIds->isNotEmpty()) {
                        $languageCount = min($languageIds->count(), random_int(1, 3));
                        $selectedLanguages = collect($languageIds->random($languageCount))->values()->all();
                        $movie->languages()->syncWithoutDetaching($selectedLanguages);
                    }

                    if ($countryIds->isNotEmpty()) {
                        $countryCount = min($countryIds->count(), random_int(1, 2));
                        $selectedCountries = collect($countryIds->random($countryCount))->values()->all();
                        $movie->countries()->syncWithoutDetaching($selectedCountries);
                    }

                    if ($personIds->isNotEmpty()) {
                        $creditCount = min($personIds->count(), random_int(4, 8));
                        $creditSelection = collect($personIds->random($creditCount))->values();

                        $movie->people()->syncWithoutDetaching(
                            $creditSelection->mapWithKeys(function (int $personId, int $index): array {
                                $isCast = $index < 3;

                                return [
                                    $personId => [
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

                    if ($watchLaterLists->isNotEmpty()) {
                        $watchlistCount = min($watchLaterLists->count(), random_int(0, 5));

                        if ($watchlistCount > 0) {
                            $selectedLists = $watchLaterLists->random($watchlistCount);

                            foreach ($selectedLists as $list) {
                                $list->items()->firstOrCreate(
                                    ['movie_id' => $movie->getKey()],
                                    ['position' => $list->nextPosition()],
                                );
                            }
                        }
                    }
                });
        });
    }
}
