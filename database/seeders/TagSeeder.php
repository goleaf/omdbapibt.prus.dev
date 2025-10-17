<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class TagSeeder extends Seeder
{
    /**
     * Seed curated system tags for discovery and moderation.
     */
    public function run(): void
    {
        if (! Schema::hasTable('tags')) {
            return;
        }

        $tags = collect($this->definitions())
            ->map(function (array $definition): array {
                $translations = Arr::get($definition, 'name_i18n', []);

                return [
                    'slug' => Arr::get($definition, 'slug'),
                    'type' => Arr::get($definition, 'type', Tag::TYPE_SYSTEM),
                    'name_i18n' => json_encode($translations, JSON_UNESCAPED_UNICODE),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        $tags->chunk(50)->each(function ($chunk): void {
            Tag::query()->upsert($chunk->all(), ['slug'], ['name_i18n', 'type', 'updated_at']);
        });

        Tag::query()
            ->where('type', Tag::TYPE_SYSTEM)
            ->whereNotIn('slug', $tags->pluck('slug'))
            ->delete();
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function definitions(): array
    {
        return [
            [
                'slug' => 'award-winning',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Award-winning',
                    'es' => 'Ganadora de premios',
                    'fr' => 'Primée',
                ],
            ],
            [
                'slug' => 'critics-choice',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Critics’ choice',
                    'es' => 'Selección de la crítica',
                    'fr' => 'Choix de la critique',
                ],
            ],
            [
                'slug' => 'family-friendly',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Family friendly',
                    'es' => 'Para toda la familia',
                    'fr' => 'Familial',
                ],
            ],
            [
                'slug' => 'festival-favorite',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Festival favorite',
                    'es' => 'Favorita del festival',
                    'fr' => 'Favori des festivals',
                ],
            ],
            [
                'slug' => 'midnight-cult',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Midnight cult',
                    'es' => 'Culto de medianoche',
                    'fr' => 'Culte de minuit',
                ],
            ],
            [
                'slug' => 'staff-pick',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Staff pick',
                    'es' => 'Selección del equipo',
                    'fr' => 'Choix de la rédaction',
                ],
            ],
            [
                'slug' => 'global-spotlight',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Global spotlight',
                    'es' => 'En foco global',
                    'fr' => 'Projecteur mondial',
                ],
            ],
            [
                'slug' => 'limited-series',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Limited series',
                    'es' => 'Serie limitada',
                    'fr' => 'Série limitée',
                ],
            ],
            [
                'slug' => 'documentary-spotlight',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Documentary spotlight',
                    'es' => 'Documental destacado',
                    'fr' => 'Documentaire à l’honneur',
                ],
            ],
            [
                'slug' => 'premiere',
                'type' => Tag::TYPE_SYSTEM,
                'name_i18n' => [
                    'en' => 'Premiere',
                    'es' => 'Estreno',
                    'fr' => 'Première',
                ],
            ],
        ];
    }
}
