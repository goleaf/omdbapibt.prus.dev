<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\UiTranslationManager;
use App\Models\User;
use App\Support\UiTranslationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UiTranslationManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_ui_translation(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->set('form.group', 'nav')
            ->set('form.key', 'cta_label')
            ->set('form.values.en', 'Stream now')
            ->set('form.values.es', 'Reproducir ahora')
            ->set('form.values.fr', 'Lire maintenant')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ui_translations', [
            'group' => 'nav',
            'key' => 'cta_label',
        ]);

        /** @var UiTranslationRepository $repository */
        $repository = app(UiTranslationRepository::class);
        $repository->refreshAndRegister();

        $this->assertSame('Reproducir ahora', trans('ui.nav.cta_label', [], 'es'));
        $this->assertSame('Lire maintenant', trans('ui.nav.cta_label', [], 'fr'));
    }

    public function test_non_admin_cannot_access_translation_manager(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.ui-translations', ['locale' => 'en']));

        $response->assertForbidden();
    }
}
