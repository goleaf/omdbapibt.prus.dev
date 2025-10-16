<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Support\SupportForm;
use App\Models\SupportRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SupportFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_support_form_renders_translated_copy(): void
    {
        app()->setLocale('es');

        Livewire::test(SupportForm::class)
            ->assertSee(__('support.form.fields.name.label'))
            ->assertSee(__('support.form.actions.submit'));

        app()->setLocale(config('app.locale'));
    }

    public function test_support_form_validates_required_fields(): void
    {
        $component = Livewire::test(SupportForm::class)
            ->call('submit')
            ->assertHasErrors([
                'name' => ['required'],
                'email' => ['required'],
                'subject' => ['required'],
                'message' => ['required'],
            ]);

        $component->assertSee(__('support.validation.name.required'))
            ->assertSee(__('support.validation.email.required'))
            ->assertSee(__('support.validation.subject.required'))
            ->assertSee(__('support.validation.message.required'));
    }

    public function test_support_request_is_persisted_and_feedback_displayed(): void
    {
        Livewire::test(SupportForm::class)
            ->set('name', 'Jane Doe')
            ->set('email', 'jane@example.com')
            ->set('subject', 'Billing question')
            ->set('message', 'Can you clarify the latest invoice?')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('statusMessage', __('support.status.submitted'))
            ->assertDispatched('support-request-submitted');

        $this->assertDatabaseHas('support_requests', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Billing question',
            'status' => SupportRequest::STATUS_PENDING,
        ]);
    }
}
