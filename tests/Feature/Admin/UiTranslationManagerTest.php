<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Livewire\Admin\UiTranslationManager;
use App\Models\UiTranslation;
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

    public function test_admin_can_update_ui_translation(): void
    {
        $admin = User::factory()->admin()->create();
        $translation = UiTranslation::factory()->create([
            'group' => 'alerts',
            'key' => 'maintenance_notice',
            'value' => [
                'en' => 'Maintenance scheduled',
                'es' => 'Mantenimiento programado',
            ],
        ]);

        Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->call('edit', $translation->id)
            ->set('form.values.en', 'Maintenance completed')
            ->set('form.values.es', 'Mantenimiento completado')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('ui_translations', [
            'id' => $translation->id,
            'value->en' => 'Maintenance completed',
            'value->es' => 'Mantenimiento completado',
        ]);
    }

    public function test_admin_can_delete_ui_translation(): void
    {
        $admin = User::factory()->admin()->create();
        $translation = UiTranslation::factory()->create([
            'group' => 'notifications',
            'key' => 'expired_plan',
            'value' => ['en' => 'Your plan expired'],
        ]);

        Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->call('confirmDeletion', $translation->id)
            ->call('deleteConfirmed')
            ->assertSet('pendingDeletionId', null)
            ->assertSet('statusMessage', __('Translation deleted.'));

        $this->assertDatabaseMissing('ui_translations', [
            'id' => $translation->id,
        ]);
    }

    public function test_authorization_is_enforced_on_livewire_actions(): void
    {
        $admin = User::factory()->admin()->create();
        $nonAdmin = User::factory()->create([
            'role' => UserRole::Subscriber->value,
        ]);
        $translation = UiTranslation::factory()->create();

        $component = Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->call('edit', $translation->id);

        $this->actingAs($nonAdmin);

        $component->call('save')->assertForbidden();

        $component = Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->set('pendingDeletionId', $translation->id);

        $this->actingAs($nonAdmin);

        $component->call('deleteConfirmed')->assertForbidden();

        $component = Livewire::actingAs($admin)
            ->test(UiTranslationManager::class);

        $this->actingAs($nonAdmin);

        $component->call('refreshCache')->assertForbidden();
    }

    public function test_non_admin_cannot_mount_translation_manager_component(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Subscriber->value,
        ]);

        Livewire::actingAs($user)
            ->test(UiTranslationManager::class)
            ->assertForbidden();
    }

    public function test_non_admin_cannot_access_translation_manager(): void
    {
        $user = User::factory()->create([
            'role' => UserRole::Subscriber->value,
        ]);

        $response = $this->actingAs($user)->get(route('admin.ui-translations', ['locale' => 'en']));

        $response->assertForbidden();
    }
}
