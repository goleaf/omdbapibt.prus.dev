<?php

namespace Tests\Unit\Support;

use App\Models\UiTranslation;
use App\Support\UiTranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiTranslationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_repository_registers_lines_into_translator(): void
    {
        UiTranslation::factory()->create([
            'group' => 'dashboard',
            'key' => 'headline',
            'value' => [
                'en' => 'Admin headline',
                'es' => 'Encabezado',
                'fr' => 'En-tête',
            ],
        ]);

        /** @var UiTranslationRepository $repository */
        $repository = app(UiTranslationRepository::class);
        $repository->refreshAndRegister();

        $this->assertSame('Encabezado', trans('ui.dashboard.headline', [], 'es'));
        $this->assertSame('En-tête', trans('ui.dashboard.headline', [], 'fr'));
    }

    public function test_repository_falls_back_when_store_is_missing(): void
    {
        config()->set('ui-translations.cache.store', 'missing-store');

        UiTranslation::factory()->create([
            'group' => 'nav',
            'key' => 'welcome',
            'value' => [
                'en' => 'Welcome',
                'es' => 'Bienvenido',
                'fr' => 'Bienvenue',
            ],
        ]);

        /** @var UiTranslationRepository $repository */
        $repository = app(UiTranslationRepository::class);
        $repository->refreshAndRegister();

        $this->assertSame('Bienvenido', trans('ui.nav.welcome', [], 'es'));
    }
}
