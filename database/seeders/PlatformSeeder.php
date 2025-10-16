<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\SeedsTranslatableTable;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    use SeedsTranslatableTable;

    public const TOTAL_PLATFORMS = 6;

    /**
     * @var array<int, array<string, mixed>>
     */
    private const RECORDS = [
        [
            'slug' => 'web-app',
            'code' => 'WEB_APP',
            'name_translations' => [
                'en' => 'Web App',
                'es' => 'Aplicación web',
                'fr' => 'Application web',
            ],
            'short_name_translations' => [
                'en' => 'Web',
                'es' => 'Web',
                'fr' => 'Web',
            ],
            'description_translations' => [
                'en' => 'Accessible from modern browsers with responsive layouts.',
                'es' => 'Accesible desde navegadores modernos con diseños responsivos.',
                'fr' => 'Accessible depuis les navigateurs modernes avec des interfaces réactives.',
            ],
            'sort_order' => 1,
            'active' => true,
            'type' => 'app',
        ],
        [
            'slug' => 'ios-app',
            'code' => 'IOS_APP',
            'name_translations' => [
                'en' => 'iOS App',
                'es' => 'Aplicación iOS',
                'fr' => 'Application iOS',
            ],
            'short_name_translations' => [
                'en' => 'iOS',
                'es' => 'iOS',
                'fr' => 'iOS',
            ],
            'description_translations' => [
                'en' => 'Optimised for iPhone and iPad with offline queueing.',
                'es' => 'Optimizada para iPhone y iPad con colas sin conexión.',
                'fr' => 'Optimisée pour iPhone et iPad avec mise en file hors ligne.',
            ],
            'sort_order' => 2,
            'active' => true,
            'type' => 'app',
        ],
        [
            'slug' => 'android-app',
            'code' => 'ANDROID_APP',
            'name_translations' => [
                'en' => 'Android App',
                'es' => 'Aplicación Android',
                'fr' => 'Application Android',
            ],
            'short_name_translations' => [
                'en' => 'Android',
                'es' => 'Android',
                'fr' => 'Android',
            ],
            'description_translations' => [
                'en' => 'Runs on phones and tablets with adaptive playback controls.',
                'es' => 'Funciona en teléfonos y tabletas con controles de reproducción adaptativos.',
                'fr' => 'Fonctionne sur téléphones et tablettes avec des contrôles de lecture adaptatifs.',
            ],
            'sort_order' => 3,
            'active' => true,
            'type' => 'app',
        ],
        [
            'slug' => 'roku-channel',
            'code' => 'ROKU_CHANNEL',
            'name_translations' => [
                'en' => 'Roku Channel',
                'es' => 'Canal Roku',
                'fr' => 'Chaîne Roku',
            ],
            'short_name_translations' => [
                'en' => 'Roku',
                'es' => 'Roku',
                'fr' => 'Roku',
            ],
            'description_translations' => [
                'en' => 'Living room experience with remote-first interactions.',
                'es' => 'Experiencia de sala con interacciones centradas en el control remoto.',
                'fr' => 'Expérience salon avec des interactions conçues pour la télécommande.',
            ],
            'sort_order' => 4,
            'active' => true,
            'type' => 'living_room',
        ],
        [
            'slug' => 'apple-tv',
            'code' => 'APPLE_TV',
            'name_translations' => [
                'en' => 'Apple TV',
                'es' => 'Apple TV',
                'fr' => 'Apple TV',
            ],
            'short_name_translations' => [
                'en' => 'Apple TV',
                'es' => 'Apple TV',
                'fr' => 'Apple TV',
            ],
            'description_translations' => [
                'en' => 'High-fidelity streaming tailored for cinematic displays.',
                'es' => 'Streaming de alta fidelidad diseñado para pantallas cinematográficas.',
                'fr' => 'Diffusion haute fidélité pensée pour les écrans cinématographiques.',
            ],
            'sort_order' => 5,
            'active' => true,
            'type' => 'living_room',
        ],
        [
            'slug' => 'fire-tv',
            'code' => 'FIRE_TV',
            'name_translations' => [
                'en' => 'Fire TV',
                'es' => 'Fire TV',
                'fr' => 'Fire TV',
            ],
            'short_name_translations' => [
                'en' => 'Fire TV',
                'es' => 'Fire TV',
                'fr' => 'Fire TV',
            ],
            'description_translations' => [
                'en' => 'Voice-enabled controls aligned with Amazon households.',
                'es' => 'Controles por voz alineados con los hogares Amazon.',
                'fr' => 'Commandes vocales intégrées pour les foyers Amazon.',
            ],
            'sort_order' => 6,
            'active' => true,
            'type' => 'living_room',
        ],
    ];

    public function run(): void
    {
        $this->seedTranslatableTable('platforms', self::RECORDS);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public static function records(): array
    {
        return self::RECORDS;
    }
}
