<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Baseline TMDB genre catalogue.
     *
     * @var array<int, array<string, int|string>>
     */
    public const GENRES = [
        [
            'name' => 'Action',
            'slug' => 'action',
            'tmdb_id' => 28,
        ],
        [
            'name' => 'Adventure',
            'slug' => 'adventure',
            'tmdb_id' => 12,
        ],
        [
            'name' => 'Animation',
            'slug' => 'animation',
            'tmdb_id' => 16,
        ],
        [
            'name' => 'Comedy',
            'slug' => 'comedy',
            'tmdb_id' => 35,
        ],
        [
            'name' => 'Crime',
            'slug' => 'crime',
            'tmdb_id' => 80,
        ],
        [
            'name' => 'Documentary',
            'slug' => 'documentary',
            'tmdb_id' => 99,
        ],
        [
            'name' => 'Drama',
            'slug' => 'drama',
            'tmdb_id' => 18,
        ],
        [
            'name' => 'Family',
            'slug' => 'family',
            'tmdb_id' => 10751,
        ],
        [
            'name' => 'Fantasy',
            'slug' => 'fantasy',
            'tmdb_id' => 14,
        ],
        [
            'name' => 'History',
            'slug' => 'history',
            'tmdb_id' => 36,
        ],
        [
            'name' => 'Horror',
            'slug' => 'horror',
            'tmdb_id' => 27,
        ],
        [
            'name' => 'Music',
            'slug' => 'music',
            'tmdb_id' => 10402,
        ],
        [
            'name' => 'Mystery',
            'slug' => 'mystery',
            'tmdb_id' => 9648,
        ],
        [
            'name' => 'Romance',
            'slug' => 'romance',
            'tmdb_id' => 10749,
        ],
        [
            'name' => 'Science Fiction',
            'slug' => 'science-fiction',
            'tmdb_id' => 878,
        ],
        [
            'name' => 'TV Movie',
            'slug' => 'tv-movie',
            'tmdb_id' => 10770,
        ],
        [
            'name' => 'Thriller',
            'slug' => 'thriller',
            'tmdb_id' => 53,
        ],
        [
            'name' => 'War',
            'slug' => 'war',
            'tmdb_id' => 10752,
        ],
        [
            'name' => 'Western',
            'slug' => 'western',
            'tmdb_id' => 37,
        ],
    ];

    /**
     * Seed the application's genre catalogue.
     */
    public function run(): void
    {
        collect(self::GENRES)->each(function (array $genre): void {
            Genre::query()->updateOrCreate(
                ['slug' => $genre['slug']],
                $genre,
            );
        });
    }
}
