<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class GenreSeeder extends Seeder
{
    public const TOTAL_GENRES = 1000;

    public function run(): void
    {
        $hasName = Schema::hasColumn('genres', 'name');

        $genres = Genre::factory()
            ->count(self::TOTAL_GENRES)
            ->sequence(function (Sequence $sequence): array {
                $position = $sequence->index + 1;
                $label = sprintf('Genre %04d', $position);

                return [
                    'slug' => sprintf('genre-%04d', $position),
                    'tmdb_id' => 20_000 + $position,
                    'name_translations' => [
                        'en' => $label,
                        'es' => sprintf('Género %04d', $position),
                        'fr' => sprintf('Genre %04d', $position),
                    ],
                ];
            })
            ->make()
            ->map(function (Genre $genre) use ($hasName): array {
                $base = [
                    'slug' => $genre->slug,
                    'tmdb_id' => $genre->tmdb_id,
                    'name_translations' => json_encode($genre->name_translations, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($hasName) {
                    $translations = $genre->name_translations ?? [];
                    $base['name'] = $translations['en'] ?? (reset($translations) ?: $genre->slug);
                }

                return $base;
            });

        collect($genres->all())
            ->chunk(200)
            ->each(function ($chunk): void {
                Genre::query()->upsert(
                    $chunk->all(),
                    ['slug'],
                    ['tmdb_id', 'name_translations', 'updated_at']
                );
            });

        Genre::query()->whereNotIn('slug', $genres->pluck('slug'))->delete();
    }
}
