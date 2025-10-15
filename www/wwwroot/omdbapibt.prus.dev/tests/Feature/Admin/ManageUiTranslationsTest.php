<?php

namespace Tests\Feature\Admin;

use App\Models\UiTranslation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManageUiTranslationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_translation_routes(): void
    {
        $response = $this->get(route('admin.translations.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_create_translation_entry(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('admin.translations.store'), [
            'key' => 'navigation.example',
            'text' => [
                'en' => 'English copy',
                'es' => 'Texto en español',
                'fr' => '',
                'xx' => 'Should be ignored',
            ],
        ]);

        $response->assertRedirect(route('admin.translations.index'));
        $response->assertSessionHas('status', trans('admin.created_success'));

        $translation = UiTranslation::where('key', 'navigation.example')->first();

        $this->assertNotNull($translation);
        $this->assertSame([
            'en' => 'English copy',
            'es' => 'Texto en español',
        ], $translation->getTranslations('text'));
    }

    public function test_authenticated_user_can_update_translation_entry(): void
    {
        $user = User::factory()->create();

        $translation = UiTranslation::create([
            'key' => 'navigation.original',
        ]);
        $translation->setTranslations('text', [
            'en' => 'Original',
            'es' => 'Original ES',
        ]);
        $translation->save();

        $response = $this->actingAs($user)->put(route('admin.translations.update', $translation), [
            'key' => 'navigation.updated',
            'text' => [
                'en' => 'Updated copy',
                'fr' => 'Copie mise à jour',
            ],
        ]);

        $response->assertRedirect(route('admin.translations.index'));
        $response->assertSessionHas('status', trans('admin.updated_success'));

        $translation->refresh();

        $this->assertSame('navigation.updated', $translation->key);
        $this->assertSame([
            'en' => 'Updated copy',
            'fr' => 'Copie mise à jour',
        ], $translation->getTranslations('text'));
    }

    public function test_authenticated_user_can_delete_translation_entry(): void
    {
        $user = User::factory()->create();

        $translation = UiTranslation::create([
            'key' => 'navigation.to-delete',
        ]);
        $translation->setTranslations('text', [
            'en' => 'Delete me',
        ]);
        $translation->save();

        $response = $this->actingAs($user)->delete(route('admin.translations.destroy', $translation));

        $response->assertRedirect(route('admin.translations.index'));
        $response->assertSessionHas('status', trans('admin.deleted_success'));

        $this->assertModelMissing($translation);
    }
}
