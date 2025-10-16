<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ReviewSeeder extends Seeder
{
    use SeedsModelsInChunks;

    private const TOTAL_REVIEWS = 1000;

    private const CHUNK_SIZE = 200;

    /**
     * Seed community reviews tied to existing users.
     */
    public function run(): void
    {
        if (Review::query()->exists()) {
            return;
        }

        $users = User::query()->select('id')->get();
        $movies = Movie::query()->select(['id', 'title'])->get();

        if ($users->isEmpty() || $movies->isEmpty()) {
            return;
        }

        $locales = $this->supportedLocales();
        $fallbackLocale = $this->fallbackLocale();

        $this->seedInChunks(self::TOTAL_REVIEWS, self::CHUNK_SIZE, function (int $count) use ($users, $movies, $locales, $fallbackLocale): void {
            $payloads = Review::factory()
                ->count($count)
                ->make(['user_id' => null])
                ->map(function (Review $review) use ($users, $movies, $locales, $fallbackLocale): array {
                    $user = $users->random();
                    $movie = $movies->random();

                    $movieTitle = $movie->title;
                    $localizedTitle = is_array($movieTitle)
                        ? ($movieTitle[$fallbackLocale] ?? reset($movieTitle) ?: 'Untitled')
                        : (string) $movieTitle;

                    return [
                        'user_id' => $user->getKey(),
                        'movie_title' => $localizedTitle,
                        'rating' => $review->rating,
                        'body' => $this->buildLocalizedReviewBody($locales, $fallbackLocale),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

            $this->chunkedInsert($payloads, 500, static fn (array $chunk): bool => Review::query()->insert($chunk));
        });
    }

    /**
     * @param  list<string>  $locales
     */
    private function buildLocalizedReviewBody(array $locales, string $fallbackLocale): string
    {
        return Collection::make($locales)
            ->map(function (string $locale) use ($fallbackLocale): string {
                $paragraphs = $locale === $fallbackLocale
                    ? fake($this->fakerLocale($locale))->paragraphs(2)
                    : fake($this->fakerLocale($locale))->paragraphs(2);

                return Collection::make($paragraphs)
                    ->map(fn (string $paragraph): string => sprintf('<p lang="%s">%s</p>', $locale, $paragraph))
                    ->implode('');
            })
            ->implode('');
    }
}
