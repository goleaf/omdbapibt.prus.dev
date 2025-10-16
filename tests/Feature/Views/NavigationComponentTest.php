<?php

namespace Tests\Feature\Views;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Tests\TestCase;

class NavigationComponentTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_renders_for_guests(): void
    {
        $this->startSession();

        $html = Blade::render('<x-layout.navigation />');

        $this->assertStringContainsString(__('ui.nav.brand.primary'), $html);
        $this->assertStringContainsString(__('ui.nav.brand.secondary'), $html);
        $this->assertStringContainsString(__('ui.nav.auth.login'), $html);
        $this->assertStringContainsString(__('ui.nav.auth.register'), $html);
    }

    public function test_navigation_renders_for_authenticated_users(): void
    {
        $this->startSession();

        $user = User::factory()->create(['name' => 'Test User']);

        $this->actingAs($user);

        $html = Blade::render('<x-layout.navigation />');

        $this->assertStringContainsString('Test User', $html);
        $this->assertStringContainsString(__('ui.nav.auth.logout'), $html);
        $this->assertStringNotContainsString(__('ui.nav.auth.login'), $html);
    }
}
