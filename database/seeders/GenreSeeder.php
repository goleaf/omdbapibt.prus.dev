<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public const TOTAL_GENRES = 1000;

    public function run(): void
    {
        $genres = Genre::factory()
            ->count(self::TOTAL_GENRES)
            ->sequence(function (Sequence $sequence): array {
                $position = $sequence->index + 1;
                $label = sprintf('Genre %04d', $position);

                return [
                    'slug' => sprintf('genre-%04d', $position),
                    'tmdb_id' => 20_000 + $position,
                    'name' => $label,
                    'name_translations' => [
                        'en' => $label,
                        'es' => sprintf('Género %04d', $position),
                        'fr' => sprintf('Genre %04d', $position),
                    ],
                ];
            })
            ->make()
            ->map(function (Genre $genre): array {
                return [
                    'slug' => $genre->slug,
                    'tmdb_id' => $genre->tmdb_id,
                    'name' => $genre->getRawOriginal('name') ?? $genre->name,
                    'name_translations' => json_encode($genre->name_translations, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        Genre::query()->upsert(
            $genres->all(),
            ['slug'],
            ['tmdb_id', 'name', 'name_translations', 'updated_at']
        );

        Genre::query()->whereNotIn('slug', $genres->pluck('slug'))->delete();
    }
}
