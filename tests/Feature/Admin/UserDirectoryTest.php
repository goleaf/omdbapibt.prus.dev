<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Livewire\Admin\UserDirectory;
use App\Models\User;
use App\Support\ImpersonationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class UserDirectoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_user_directory(): void
    {
        $admin = User::factory()->admin()->create();
        $users = User::factory()->count(3)->create();

        $this->actingAs($admin);

        Livewire::test(UserDirectory::class)
            ->assertOk()
            ->assertSee($users->first()->email);
    }

    public function test_role_can_be_updated(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $this->actingAs($admin);

        Livewire::test(UserDirectory::class)
            ->call('updateRole', $user->id, UserRole::Subscriber->value);

        $this->assertSame(UserRole::Subscriber, $user->fresh()->role);
    }

    public function test_non_admin_cannot_update_role(): void
    {
        $admin = User::factory()->admin()->create();
        $nonAdmin = User::factory()->create();
        $target = User::factory()->create();

        $component = Livewire::actingAs($admin)->test(UserDirectory::class);

        $this->actingAs($nonAdmin);

        $component->call('updateRole', $target->id, UserRole::Subscriber->value)
            ->assertForbidden();

        $this->assertSame(UserRole::User, $target->fresh()->role);
    }

    public function test_admin_can_impersonate_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('impersonate', $target->id);

        $this->assertSame($admin->id, session()->get(ImpersonationManager::SESSION_KEY));
        $this->assertSame($target->id, Auth::id());
    }

    public function test_non_admin_cannot_impersonate_users(): void
    {
        $admin = User::factory()->admin()->create();
        $nonAdmin = User::factory()->create();
        $target = User::factory()->create();

        $component = Livewire::actingAs($admin)->test(UserDirectory::class);

        $this->actingAs($nonAdmin);

        $component->call('impersonate', $target->id)
            ->assertForbidden();

        $this->assertNull(session()->get(ImpersonationManager::SESSION_KEY));
        $this->assertSame($nonAdmin->id, Auth::id());
    }

    public function test_impersonated_user_is_authorized_to_stop_impersonating(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('impersonate', $target->id);

        $this->assertSame($target->id, Auth::id());
        $this->assertTrue(session()->has(ImpersonationManager::SESSION_KEY));
        $this->assertTrue(Gate::allows('stopImpersonating', User::class));

        app(ImpersonationManager::class)->stop();

        $this->assertSame($admin->id, Auth::id());
        $this->assertNull(session()->get(ImpersonationManager::SESSION_KEY));
    }

    public function test_non_admin_cannot_access_directory(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get(route('admin.users'))->assertForbidden();
    }
}
