<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class TagSeeder extends Seeder
{
    /**
     * Seed curated system tags used across the catalog.
     */
    public function run(): void
    {
        if (! Schema::hasTable('tags')) {
            return;
        }

        $definitions = [
            [
                'slug' => 'award-winning',
                'translations' => [
                    'en' => 'Award-winning',
                    'es' => 'Ganadora de premios',
                    'fr' => 'Primée',
                ],
            ],
            [
                'slug' => 'critics-choice',
                'translations' => [
                    'en' => 'Critics’ choice',
                    'es' => 'Selección de la crítica',
                    'fr' => 'Choix de la critique',
                ],
            ],
            [
                'slug' => 'family-friendly',
                'translations' => [
                    'en' => 'Family friendly',
                    'es' => 'Para toda la familia',
                    'fr' => 'Familial',
                ],
            ],
            [
                'slug' => 'documentary-spotlight',
                'translations' => [
                    'en' => 'Documentary spotlight',
                    'es' => 'Documental destacado',
                    'fr' => 'Documentaire à l’honneur',
                ],
            ],
            [
                'slug' => 'staff-pick',
                'translations' => [
                    'en' => 'Staff pick',
                    'es' => 'Selección del equipo',
                    'fr' => 'Choix de la rédaction',
                ],
            ],
            [
                'slug' => 'global-spotlight',
                'translations' => [
                    'en' => 'Global spotlight',
                    'es' => 'En foco global',
                    'fr' => 'Projecteur mondial',
                ],
            ],
        ];

        foreach ($definitions as $definition) {
            Tag::query()->updateOrCreate(
                ['slug' => $definition['slug']],
                [
                    'type' => Tag::TYPE_SYSTEM,
                    'name_i18n' => json_encode($definition['translations'], JSON_UNESCAPED_UNICODE),
                ],
            );
        }

        Tag::query()
            ->where('type', Tag::TYPE_SYSTEM)
            ->whereNotIn('slug', array_column($definitions, 'slug'))
            ->delete();
    }
}
