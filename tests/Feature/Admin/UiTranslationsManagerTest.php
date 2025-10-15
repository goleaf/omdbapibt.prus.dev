<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\UiTranslationsManager;
use App\Models\UiTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Tests\TestCase;

class UiTranslationsManagerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/logout', fn () => redirect('/'))
            ->name('logout');
    }

    public function test_admin_can_access_manager_route(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get('/admin/ui-translations')
            ->assertOk()
            ->assertSee('UI Translations');
    }

    public function test_non_admin_cannot_access_manager_route(): void
    {
        $user = User::factory()->create([
            'role' => 'subscriber',
        ]);

        $this->actingAs($user)
            ->get('/admin/ui-translations')
            ->assertForbidden();
    }

    public function test_admin_can_create_translation(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $component = Livewire::actingAs($admin)
            ->test(UiTranslationsManager::class)
            ->set('form.group', 'navigation')
            ->set('form.key', 'hero_cta')
            ->set('form.translations.en', 'Stream now')
            ->set('form.translations.es', 'Reproduce ahora')
            ->set('form.translations.fr', 'Regarder maintenant')
            ->call('save')
            ->assertHasNoErrors()
            ->assertSet('form.group', '')
            ->assertSet('form.key', '');

        foreach (config('translatable.locales') as $locale) {
            $component->assertSet("form.translations.{$locale}", '');
        }

        $this->assertDatabaseHas('ui_translations', [
            'group' => 'navigation',
            'key' => 'hero_cta',
        ]);

        $record = UiTranslation::query()
            ->where('group', 'navigation')
            ->where('key', 'hero_cta')
            ->first();

        $this->assertNotNull($record);
        $this->assertSame('Stream now', $record->getTranslation('value', 'en'));
        $this->assertSame('Reproduce ahora', $record->getTranslation('value', 'es'));
        $this->assertSame('Regarder maintenant', $record->getTranslation('value', 'fr'));
    }
}
