<?php

namespace Tests\Unit\Admin;

use App\Livewire\Admin\Forms\UiTranslationForm;
use App\Models\UiTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Tests\TestCase;

class UiTranslationFormTest extends TestCase
{
    use RefreshDatabase;

    protected function makeForm(): UiTranslationForm
    {
        $component = new class extends Component
        {
            public function render()
            {
                return view('tests::stub');
            }
        };

        $component->setId('test-component');
        $component->setName('test-component');

        return new UiTranslationForm($component, 'form');
    }

    public function test_persist_normalizes_keys_and_trims_values(): void
    {
        $form = $this->makeForm();
        $form->configure(['en', 'es', 'fr', 'ru'], 'en');
        $form->startCreating();

        $form->group = 'Nav Links';
        $form->key = 'CTA Label';
        $form->values['en'] = '  Stream now  ';
        $form->values['es'] = '  Reproducir ahora  ';
        $form->values['fr'] = '   ';

        $saved = $form->persist();

        $this->assertDatabaseHas('ui_translations', [
            'id' => $saved->id,
            'group' => 'nav_links',
            'key' => 'cta_label',
        ]);

        $this->assertSame([
            'en' => 'Stream now',
            'es' => 'Reproducir ahora',
        ], $saved->getTranslations('value'));
    }

    public function test_duplicate_key_for_normalized_group_fails_validation(): void
    {
        UiTranslation::factory()->create([
            'group' => 'nav_links',
            'key' => 'cta_label',
            'value' => ['en' => 'Existing value'],
        ]);

        $form = $this->makeForm();
        $form->configure(['en', 'es'], 'en');
        $form->startCreating();

        $form->group = 'Nav Links';
        $form->key = 'CTA Label';
        $form->values['en'] = 'Another value';
        $form->values['es'] = '';

        try {
            $form->persist();
            $this->fail('Expected ValidationException was not thrown.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('form.key', $exception->errors());
            $this->assertSame([
                trans('admin.ui_translations.validation.key_unique', [
                    'group' => 'nav_links',
                    'key' => 'cta_label',
                ]),
            ], $exception->errors()['form.key']);
        }
    }
}
