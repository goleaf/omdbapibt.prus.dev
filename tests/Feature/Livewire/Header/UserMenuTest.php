<?php

namespace Tests\Feature\Livewire\Header;

use App\Livewire\Header\UserMenu;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class UserMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_renders_successfully_when_authenticated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->assertStatus(200)
            ->assertViewHas('user')
            ->assertSee($user->name);
    }

    public function test_it_toggles_dropdown_open_and_closed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->assertSet('isOpen', false)
            ->call('toggle')
            ->assertSet('isOpen', true)
            ->call('toggle')
            ->assertSet('isOpen', false);
    }

    public function test_it_closes_dropdown(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->set('isOpen', true)
            ->call('close')
            ->assertSet('isOpen', false);
    }

    public function test_it_responds_to_close_all_dropdowns_event(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->set('isOpen', true)
            ->dispatch('closeAllDropdowns')
            ->assertSet('isOpen', false);
    }

    public function test_it_displays_user_information(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->assertSee('John Doe')
            ->assertSee('john@example.com');
    }

    public function test_it_shows_admin_link_for_admin_users(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin);

        Livewire::test(UserMenu::class)
            ->assertSee(__('ui.nav.links.admin'));
    }

    public function test_it_hides_admin_link_for_regular_users(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->assertDontSee(__('ui.nav.links.admin'));
    }

    public function test_it_displays_account_link(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->assertSee(__('ui.nav.user_menu.account'));
    }

    public function test_it_displays_logout_button(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Livewire::test(UserMenu::class)
            ->assertSee(__('ui.nav.auth.logout'));
    }
}
