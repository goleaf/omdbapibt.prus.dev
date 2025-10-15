<?php

namespace Tests\Feature\Livewire\Admin;

use App\Livewire\Admin\UiTranslationManager;
use App\Models\UiTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UiTranslationManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_validation_errors_are_localized(): void
    {
        $admin = User::factory()->admin()->create();

        app()->setLocale('es');

        Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->call('save')
            ->assertHasErrors([
                'form.group' => 'required',
                'form.key' => 'required',
                'form.values.en' => 'required',
            ])
            ->assertSee('Se requiere un valor de traducción para el idioma EN.');
    }

    public function test_success_message_is_localized(): void
    {
        $admin = User::factory()->admin()->create();

        app()->setLocale('fr');

        Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->set('form.group', 'navigation')
            ->set('form.key', 'cta_label')
            ->set('form.values.en', 'Call to action')
            ->set('form.values.fr', 'Appel à l’action')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSee('Traduction enregistrée.');

        $this->assertDatabaseHas('ui_translations', [
            'group' => 'navigation',
            'key' => 'cta_label',
        ]);

        $translation = UiTranslation::query()->first();
        $this->assertNotNull($translation);
        $this->assertSame('Call to action', $translation->getTranslation('value', 'en'));
        $this->assertSame('Appel à l’action', $translation->getTranslation('value', 'fr'));
    }
}
