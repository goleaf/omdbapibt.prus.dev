<?php

namespace Tests\Feature\Impersonation;

use App\Models\User;
use App\Support\ImpersonationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StopImpersonationTest extends TestCase
{
    use RefreshDatabase;

    public function test_banner_is_visible_and_route_restores_admin_account(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin);

        app(ImpersonationManager::class)->start($admin, $user);

        $response = $this->get(localized_route('home'));

        $response->assertOk();
        $response->assertSeeText(__('ui.impersonation.banner_title', ['name' => $user->name]));
        $response->assertSeeText(__('ui.impersonation.stop'));

        $stopResponse = $this->delete(localized_route('impersonation.stop'));

        $stopResponse->assertRedirect(localized_route('admin.users'));
        $stopResponse->assertSessionHas('status', __('ui.impersonation.stopped'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_stop_route_gracefully_handles_missing_impersonation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        $response = $this->delete(localized_route('impersonation.stop'));

        $response->assertRedirect(localized_route('home'));
    }
}
