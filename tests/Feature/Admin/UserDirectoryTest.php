<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Livewire\Admin\UserDirectory;
use App\Models\User;
use App\Support\ImpersonationManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

    public function test_non_admin_cannot_update_roles(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $target = User::factory()->create();

        $component = Livewire::actingAs($admin)
            ->test(UserDirectory::class);

        $this->actingAs($user);

        $component->call('updateRole', $target->id, UserRole::Subscriber->value)->assertForbidden();

        $this->assertSame(UserRole::User, $target->fresh()->role);
    }

    public function test_admin_can_export_user_directory(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('exportCsv')
            ->assertStatus(200);
    }

    public function test_non_admin_cannot_export_user_directory(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();

        $component = Livewire::actingAs($admin)
            ->test(UserDirectory::class);

        $this->actingAs($user);

        $component->call('exportCsv')->assertForbidden();
    }

    public function test_admin_can_impersonate_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $manager = app(ImpersonationManager::class);

        $this->assertFalse($manager->isImpersonating());

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('impersonate', $target->id);

        $this->assertTrue($manager->isImpersonating());
        $this->assertSame($admin->getKey(), $manager->impersonator()?->getKey());
        $this->assertSame($target->getKey(), auth()->id());

        $manager->stop();
    }

    public function test_non_admin_cannot_impersonate_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->create();
        $target = User::factory()->create();

        $manager = app(ImpersonationManager::class);

        $component = Livewire::actingAs($admin)
            ->test(UserDirectory::class);

        $this->actingAs($user);

        $component->call('impersonate', $target->id)->assertForbidden();

        $this->assertFalse($manager->isImpersonating());
        $this->assertSame($user->getKey(), auth()->id());
    }

    public function test_non_admin_cannot_access_directory(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get(route('admin.users'))->assertForbidden();
    }
}
