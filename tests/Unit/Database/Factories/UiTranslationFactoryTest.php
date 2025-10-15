<?php

namespace Tests\Unit\Database\Factories;

use App\Models\UiTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiTranslationFactoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_ui_translation_factory_creates_localized_values(): void
    {
        $translation = UiTranslation::factory()->create();

        $this->assertNotEmpty($translation->group);
        $this->assertNotEmpty($translation->key);
        $translations = $translation->getTranslations('value');

        $this->assertArrayHasKey('en', $translations);
        $this->assertArrayHasKey('es', $translations);
        $this->assertArrayHasKey('fr', $translations);
    }
}
