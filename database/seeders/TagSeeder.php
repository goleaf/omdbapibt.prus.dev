<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsTranslatableTable;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    use SeedsTranslatableTable;

    public const TOTAL_TAGS = 6;

    /**
     * @var array<int, array<string, mixed>>
     */
    private const RECORDS = [
        [
            'slug' => 'new-release-spotlight',
            'code' => 'NEW_RELEASE_SPOTLIGHT',
            'name_translations' => [
                'en' => 'New Release Spotlight',
                'es' => 'Estreno destacado',
                'fr' => 'Nouvelle sortie à la une',
            ],
            'description_translations' => [
                'en' => 'Fresh arrivals from the last seven days ready for promotion.',
                'es' => 'Estrenos recientes de los últimos siete días listos para destacar.',
                'fr' => 'Les nouveautés de la semaine prêtes à être mises en avant.',
            ],
            'sort_order' => 1,
            'active' => true,
        ],
        [
            'slug' => 'fan-favorites',
            'code' => 'FAN_FAVORITES',
            'name_translations' => [
                'en' => 'Fan Favorites',
                'es' => 'Favoritos de la audiencia',
                'fr' => 'Favoris du public',
            ],
            'description_translations' => [
                'en' => 'Curated picks with sustained audience demand across regions.',
                'es' => 'Selecciones curadas con demanda constante en todas las regiones.',
                'fr' => 'Sélections soignées avec une demande soutenue dans toutes les régions.',
            ],
            'sort_order' => 2,
            'active' => true,
        ],
        [
            'slug' => 'critics-choice',
            'code' => 'CRITICS_CHOICE',
            'name_translations' => [
                'en' => 'Critics Choice',
                'es' => 'Selección de la crítica',
                'fr' => 'Choix de la critique',
            ],
            'description_translations' => [
                'en' => 'Award-calibre titles backed by top reviewer sentiment.',
                'es' => 'Títulos de calibre premiado respaldados por la crítica especializada.',
                'fr' => 'Des titres dignes de récompenses portés par les meilleurs critiques.',
            ],
            'sort_order' => 3,
            'active' => true,
        ],
        [
            'slug' => 'late-night-thrills',
            'code' => 'LATE_NIGHT_THRILLS',
            'name_translations' => [
                'en' => 'Late Night Thrills',
                'es' => 'Emociones nocturnas',
                'fr' => 'Sensations nocturnes',
            ],
            'description_translations' => [
                'en' => 'Pulse-pounding stories ideal for post-primetime marathons.',
                'es' => 'Historias llenas de adrenalina ideales para maratones nocturnos.',
                'fr' => 'Des récits haletants parfaits pour des marathons après la primetime.',
            ],
            'sort_order' => 4,
            'active' => true,
        ],
        [
            'slug' => 'family-movie-night',
            'code' => 'FAMILY_MOVIE_NIGHT',
            'name_translations' => [
                'en' => 'Family Movie Night',
                'es' => 'Noche de cine en familia',
                'fr' => 'Soirée cinéma en famille',
            ],
            'description_translations' => [
                'en' => 'Feel-good programming with broad parental and kid appeal.',
                'es' => 'Programación entrañable con atractivo para padres y niños.',
                'fr' => 'Des programmes chaleureux qui plaisent aux parents comme aux enfants.',
            ],
            'sort_order' => 5,
            'active' => true,
        ],
        [
            'slug' => 'documentary-deep-dives',
            'code' => 'DOCUMENTARY_DEEP_DIVES',
            'name_translations' => [
                'en' => 'Documentary Deep Dives',
                'es' => 'Documentales a fondo',
                'fr' => 'Explorations documentaires',
            ],
            'description_translations' => [
                'en' => 'Investigative features that unpack timely cultural moments.',
                'es' => 'Producciones investigativas que analizan momentos culturales actuales.',
                'fr' => 'Des enquêtes documentaires qui décryptent l’actualité culturelle.',
            ],
            'sort_order' => 6,
            'active' => true,
        ],
    ];

    public function run(): void
    {
        $this->seedTranslatableTable('tags', self::RECORDS);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function records(): array
    {
        return self::RECORDS;
    }
}
