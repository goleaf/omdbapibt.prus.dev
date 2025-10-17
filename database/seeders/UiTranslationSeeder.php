<?php

namespace Database\Seeders;

use App\Models\UiTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UiTranslationSeeder extends Seeder
{
    /**
     * Seed translated UI labels for the demo environment.
     */
    public function run(): void
    {
        if (! Schema::hasTable('ui_translations')) {
            return;
        }

        if (UiTranslation::query()->exists()) {
            return;
        }

        $navTranslations = [
            'brand.primary' => [
                'en' => 'OMDb',
                'es' => 'OMDb',
                'fr' => 'OMDb',
            ],
            'brand.secondary' => [
                'en' => 'Stream',
                'es' => 'Stream',
                'fr' => 'Stream',
            ],
            'links.home' => [
                'en' => 'Home',
                'es' => 'Inicio',
                'fr' => 'Accueil',
            ],
            'links.browse' => [
                'en' => 'Browse',
                'es' => 'Explorar',
                'fr' => 'Explorer',
            ],
            'links.pricing' => [
                'en' => 'Pricing',
                'es' => 'Precios',
                'fr' => 'Tarifs',
            ],
            'links.components' => [
                'en' => 'UI components',
                'es' => 'Componentes de la interfaz de usuario',
                'fr' => 'Composants de l’interface utilisateur',
            ],
            'theme.dark' => [
                'en' => 'Dark mode',
                'es' => 'Modo oscuro',
                'fr' => 'Mode sombre',
            ],
            'auth.login' => [
                'en' => 'Sign in',
                'es' => 'Iniciar sesión',
                'fr' => 'Connexion',
            ],
            'auth.register' => [
                'en' => 'Join now',
                'es' => 'Únete ahora',
                'fr' => 'Rejoindre',
            ],
            'footer.terms' => [
                'en' => 'Terms',
                'es' => 'Términos',
                'fr' => 'Conditions',
            ],
            'footer.privacy' => [
                'en' => 'Privacy',
                'es' => 'Privacidad',
                'fr' => 'Confidentialité',
            ],
            'footer.support' => [
                'en' => 'Support',
                'es' => 'Soporte',
                'fr' => 'Assistance',
            ],
            'footer.copyright' => [
                'en' => '© :year OMDb Stream. All rights reserved.',
                'es' => '© :year OMDb Stream. Todos los derechos reservados.',
                'fr' => '© :year OMDb Stream. Tous droits réservés.',
            ],
        ];

        foreach ($navTranslations as $key => $value) {
            UiTranslation::query()->create([
                'group' => 'nav',
                'key' => $key,
                'value' => $value,
            ]);
        }

        UiTranslation::factory()->count(1_000)->create();
    }
}
