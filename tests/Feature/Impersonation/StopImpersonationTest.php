<?php

namespace Tests\Feature\Impersonation;

use App\Enums\UserManagementAction;
use App\Models\User;
use App\Support\ImpersonationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StopImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_impersonation_can_be_stopped_via_route(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();
        $locale = config('app.fallback_locale');

        $this->actingAs($admin);

        $impersonationManager = app(ImpersonationManager::class);
        $impersonationManager->start($admin, $target);

        $response = $this->from(route('home', ['locale' => $locale]))
            ->post(route('impersonation.stop', ['locale' => $locale]));

        $response->assertRedirect(route('home', ['locale' => $locale]));

        $this->assertFalse($impersonationManager->isImpersonating());
        $this->assertAuthenticatedAs($admin);

        $this->assertDatabaseHas('user_management_logs', [
            'action' => UserManagementAction::ImpersonationStopped->value,
            'actor_id' => $admin->getKey(),
        ]);
    }

    public function test_stopping_without_active_impersonation_is_a_noop(): void
    {
        $admin = User::factory()->admin()->create();
        $locale = config('app.fallback_locale');

        $this->actingAs($admin);

        $response = $this->from(route('dashboard', ['locale' => $locale]))
            ->post(route('impersonation.stop', ['locale' => $locale]));

        $response->assertRedirect(route('dashboard', ['locale' => $locale]));
        $this->assertAuthenticatedAs($admin);

        $this->assertDatabaseMissing('user_management_logs', [
            'action' => UserManagementAction::ImpersonationStopped->value,
        ]);
    }
}
