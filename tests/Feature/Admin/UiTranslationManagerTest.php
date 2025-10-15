<?php

namespace Tests\Feature\Admin;

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

        Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->call('refreshCache')
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

    public function test_admin_can_delete_ui_translation(): void
    {
        $admin = User::factory()->admin()->create();
        $translation = UiTranslation::factory()->create([
            'group' => 'nav',
            'key' => 'cta_label',
            'value' => ['en' => 'Stream now'],
        ]);

        Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->call('confirmDeletion', $translation->id)
            ->call('deleteConfirmed')
            ->assertHasNoErrors();

        $this->assertDatabaseMissing('ui_translations', [
            'id' => $translation->id,
        ]);
    }

    public function test_non_admin_cannot_save_translations(): void
    {
        $admin = User::factory()->admin()->create();
        $nonAdmin = User::factory()->create();

        $component = Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->set('form.group', 'nav')
            ->set('form.key', 'cta_label')
            ->set('form.values.en', 'Stream now');

        $snapshot = json_encode($component->snapshot);

        $response = $this->actingAs($nonAdmin)
            ->withHeaders([
                'X-Livewire' => 'true',
                'Accept' => 'application/json',
            ])
            ->post('/livewire/update', [
                'components' => [
                    [
                        'snapshot' => $snapshot,
                        'updates' => [],
                        'calls' => [
                            [
                                'method' => 'save',
                                'params' => [],
                                'path' => '',
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_delete_translations(): void
    {
        $admin = User::factory()->admin()->create();
        $nonAdmin = User::factory()->create();
        $translation = UiTranslation::factory()->create([
            'group' => 'nav',
            'key' => 'cta_label',
            'value' => ['en' => 'Stream now'],
        ]);

        $component = Livewire::actingAs($admin)
            ->test(UiTranslationManager::class)
            ->call('confirmDeletion', $translation->id);

        $snapshot = json_encode($component->snapshot);

        $response = $this->actingAs($nonAdmin)
            ->withHeaders([
                'X-Livewire' => 'true',
                'Accept' => 'application/json',
            ])
            ->post('/livewire/update', [
                'components' => [
                    [
                        'snapshot' => $snapshot,
                        'updates' => [],
                        'calls' => [
                            [
                                'method' => 'deleteConfirmed',
                                'params' => [],
                                'path' => '',
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertForbidden();
    }

    public function test_non_admin_cannot_refresh_translation_cache(): void
    {
        $admin = User::factory()->admin()->create();
        $nonAdmin = User::factory()->create();

        $component = Livewire::actingAs($admin)
            ->test(UiTranslationManager::class);

        $snapshot = json_encode($component->snapshot);

        $response = $this->actingAs($nonAdmin)
            ->withHeaders([
                'X-Livewire' => 'true',
                'Accept' => 'application/json',
            ])
            ->post('/livewire/update', [
                'components' => [
                    [
                        'snapshot' => $snapshot,
                        'updates' => [],
                        'calls' => [
                            [
                                'method' => 'refreshCache',
                                'params' => [],
                                'path' => '',
                            ],
                        ],
                    ],
                ],
            ]);

        $response->assertForbidden();
    }
}
