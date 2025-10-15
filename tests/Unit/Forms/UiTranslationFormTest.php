<?php

namespace Tests\Unit\Forms;

use App\Livewire\Admin\Forms\UiTranslationForm;
use App\Models\UiTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Tests\TestCase;

class UiTranslationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_fallback_locale_value_is_required(): void
    {
        $form = $this->makeForm();
        $form->setLocales(['en', 'es'], 'en');
        $form->group = 'nav';
        $form->key = 'cta';
        $form->values = [
            'en' => '',
            'es' => 'Guardar',
        ];

        try {
            $form->validate();
            $this->fail('ValidationException was not thrown for missing fallback locale value.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('form.values.en', $exception->errors());
            $this->assertSame(
                __('ui.admin.ui_translations.validation.value_required', ['locale' => 'EN']),
                $exception->errors()['form.values.en'][0]
            );
        }
    }

    public function test_payload_normalizes_group_key_and_values(): void
    {
        $form = $this->makeForm();
        $form->setLocales(['en', 'es'], 'en');
        $form->group = 'Main Navigation';
        $form->key = 'CTA Label';
        $form->values = [
            'en' => '  Save  ',
            'es' => '  Guardar  ',
        ];

        $form->validate();

        $payload = $form->payload();

        $this->assertSame('main_navigation', $payload['group']);
        $this->assertSame('cta_label', $payload['key']);
        $this->assertSame([
            'en' => 'Save',
            'es' => 'Guardar',
        ], $payload['values']);
    }

    public function test_unique_key_rule_ignores_current_translation(): void
    {
        $existing = UiTranslation::create([
            'group' => 'nav',
            'key' => 'cta',
            'value' => ['en' => 'CTA'],
        ]);

        $form = $this->makeForm();
        $form->setLocales(['en'], 'en');
        $form->group = 'nav';
        $form->key = 'cta';
        $form->values = ['en' => 'Duplicate'];

        try {
            $form->validate();
            $this->fail('ValidationException was not thrown for duplicate key.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('form.key', $exception->errors());
            $this->assertSame(
                __('ui.admin.ui_translations.validation.key_unique'),
                $exception->errors()['form.key'][0]
            );
        }

        $form->translationId = $existing->id;
        $form->setLocales(['en'], 'en');
        $form->validate();

        $this->assertTrue(true, 'Validation passed when editing existing translation.');
    }

    private function makeForm(): UiTranslationForm
    {
        $component = new class extends Component
        {
            public function render()
            {
                return view('livewire::stub');
            }
        };

        return new UiTranslationForm($component, 'form');
    }
}
