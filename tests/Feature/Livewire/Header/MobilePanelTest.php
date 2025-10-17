<?php

namespace Tests\Feature\Livewire\Header;

use App\Livewire\Header\MobilePanel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MobilePanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_successfully(): void
    {
        Livewire::test(MobilePanel::class)
            ->assertStatus(200)
            ->assertViewHas('user')
            ->assertViewHas('hasLogin')
            ->assertViewHas('hasRegister');
    }

    public function test_it_starts_closed(): void
    {
        Livewire::test(MobilePanel::class)
            ->assertSet('isOpen', false);
    }

    public function test_it_toggles_on_event(): void
    {
        Livewire::test(MobilePanel::class)
            ->assertSet('isOpen', false)
            ->dispatch('toggleMobileMenu')
            ->assertSet('isOpen', true)
            ->dispatch('toggleMobileMenu')
            ->assertSet('isOpen', false);
    }

    public function test_it_closes_panel(): void
    {
        Livewire::test(MobilePanel::class)
            ->set('isOpen', true)
            ->call('close')
            ->assertSet('isOpen', false);
    }

    public function test_it_displays_navigation_links_for_guests(): void
    {
        Livewire::test(MobilePanel::class)
            ->assertSee(__('ui.nav.links.home'))
            ->assertSee(__('ui.nav.links.browse'))
            ->assertSee(__('ui.nav.links.pricing'));
    }

    public function test_it_displays_auth_buttons_for_guests(): void
    {
        Livewire::test(MobilePanel::class)
            ->assertSee(__('ui.nav.auth.login'))
            ->assertSee(__('ui.nav.auth.register'));
    }

    public function test_it_displays_user_info_when_authenticated(): void
    {
        $user = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $this->actingAs($user);

        Livewire::test(MobilePanel::class)
            ->assertSee('Jane Doe')
            ->assertSee('jane@example.com');
    }

    public function test_it_displays_account_link_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(MobilePanel::class)
            ->assertSee(__('ui.nav.user_menu.account'));
    }

    public function test_it_displays_logout_button_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(MobilePanel::class)
            ->assertSee(__('ui.nav.auth.logout'));
    }

    public function test_it_hides_auth_buttons_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $component = Livewire::test(MobilePanel::class);

        // Should see logout but not login/register
        $component->assertSee(__('ui.nav.auth.logout'));
        $component->assertDontSee(__('ui.nav.auth.login'));
        $component->assertDontSee(__('ui.nav.auth.register'));
    }

    public function test_it_displays_admin_link_for_admin_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(MobilePanel::class)
            ->assertSee(__('ui.nav.links.admin'));
    }

    public function test_it_hides_admin_link_for_regular_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(MobilePanel::class)
            ->assertDontSee(__('ui.nav.links.admin'));
    }

    public function test_it_displays_menu_label(): void
    {
        Livewire::test(MobilePanel::class)
            ->assertSee(__('ui.nav.menu.label'));
    }

    public function test_it_displays_close_button_label(): void
    {
        Livewire::test(MobilePanel::class)
            ->assertSee(__('ui.nav.menu.close'));
    }
}
