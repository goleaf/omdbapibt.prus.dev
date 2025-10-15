<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenresTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $genres = [
            ['tmdb_id' => 28, 'name' => 'Action', 'slug' => 'action'],
            ['tmdb_id' => 12, 'name' => 'Adventure', 'slug' => 'adventure'],
            ['tmdb_id' => 16, 'name' => 'Animation', 'slug' => 'animation'],
            ['tmdb_id' => 35, 'name' => 'Comedy', 'slug' => 'comedy'],
            ['tmdb_id' => 80, 'name' => 'Crime', 'slug' => 'crime'],
            ['tmdb_id' => 99, 'name' => 'Documentary', 'slug' => 'documentary'],
            ['tmdb_id' => 18, 'name' => 'Drama', 'slug' => 'drama'],
            ['tmdb_id' => 10751, 'name' => 'Family', 'slug' => 'family'],
            ['tmdb_id' => 14, 'name' => 'Fantasy', 'slug' => 'fantasy'],
            ['tmdb_id' => 36, 'name' => 'History', 'slug' => 'history'],
            ['tmdb_id' => 27, 'name' => 'Horror', 'slug' => 'horror'],
            ['tmdb_id' => 10402, 'name' => 'Music', 'slug' => 'music'],
            ['tmdb_id' => 9648, 'name' => 'Mystery', 'slug' => 'mystery'],
            ['tmdb_id' => 10749, 'name' => 'Romance', 'slug' => 'romance'],
            ['tmdb_id' => 878, 'name' => 'Science Fiction', 'slug' => 'science-fiction'],
            ['tmdb_id' => 10770, 'name' => 'TV Movie', 'slug' => 'tv-movie'],
            ['tmdb_id' => 53, 'name' => 'Thriller', 'slug' => 'thriller'],
            ['tmdb_id' => 10752, 'name' => 'War', 'slug' => 'war'],
            ['tmdb_id' => 37, 'name' => 'Western', 'slug' => 'western'],
        ];

        $payload = collect($genres)->map(function (array $genre) use ($now) {
            return array_merge($genre, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        })->all();

        DB::table('genres')->upsert($payload, ['slug'], ['name', 'tmdb_id', 'updated_at']);
    }
}
