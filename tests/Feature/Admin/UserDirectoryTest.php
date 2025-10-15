<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Livewire\Admin\UserDirectory;
use App\Models\User;
use App\Support\ImpersonationManager;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Symfony\Component\HttpFoundation\StreamedResponse;
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
        $moderator = User::factory()->create();
        $target = User::factory()->create();

        Livewire::actingAs($moderator)
            ->test(UserDirectory::class)
            ->assertForbidden();

        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)->test(UserDirectory::class);

        $this->actingAs($moderator);

        $component
            ->call('updateRole', $target->id, UserRole::Subscriber->value)
            ->assertForbidden();
    }

    public function test_admin_cannot_update_their_own_role(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('updateRole', $admin->id, UserRole::Subscriber->value)
            ->assertForbidden();

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
    }

    public function test_non_admin_cannot_access_directory(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get(route('admin.users'))->assertForbidden();
    }

    public function test_csv_export_respects_active_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $matching = User::factory()->create(['name' => 'Alpha Match', 'email' => 'alpha@example.com']);
        $nonMatching = User::factory()->create([
            'name' => 'Beta User',
            'email' => 'beta@example.com',
            'role' => UserRole::Subscriber,
        ]);

        $this->actingAs($admin);

        $component = app(UserDirectory::class);
        $component->boot(app(ImpersonationManager::class));
        $component->mount();

        $component->search = 'Alpha';
        $component->roleFilter = UserRole::User->value;

        $response = $component->exportCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);

        ob_start();
        $response->sendContent();
        $csv = ob_get_clean();

        $this->assertStringContainsString($matching->email, $csv);
        $this->assertStringNotContainsString($nonMatching->email, $csv);

        $lines = array_values(array_filter(explode("\n", trim($csv))));
        $this->assertNotEmpty($lines);
        $this->assertSame(['Name', 'Email', 'Role', 'Watch Events', 'Joined'], str_getcsv($lines[0]));
    }

    public function test_non_admin_cannot_export_or_impersonate(): void
    {
        $moderator = User::factory()->create();
        $target = User::factory()->create();

        $admin = User::factory()->admin()->create();

        $component = Livewire::actingAs($admin)->test(UserDirectory::class);

        $this->actingAs($moderator);

        $component->call('exportCsv')->assertForbidden();

        $component = Livewire::actingAs($admin)->test(UserDirectory::class);

        $this->actingAs($moderator);

        $component->call('impersonate', $target->id)->assertForbidden();
    }

    public function test_admin_can_impersonate_user(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->create();

        $manager = app(ImpersonationManager::class);

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('impersonate', $target->id);

        $this->assertTrue($manager->isImpersonating());
        $this->assertAuthenticatedAs($target);

        $manager->stop($target);
    }

    public function test_admin_cannot_impersonate_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('impersonate', $admin->id)
            ->assertForbidden();
    }

    public function test_admin_cannot_impersonate_other_admins(): void
    {
        $admin = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->call('impersonate', $otherAdmin->id)
            ->assertForbidden();
    }

    public function test_impersonated_admin_loses_access_to_admin_actions(): void
    {
        $admin = User::factory()->admin()->create();
        $impersonated = User::factory()->create();
        $target = User::factory()->create();

        $manager = app(ImpersonationManager::class);

        $this->actingAs($admin);

        $component = Livewire::test(UserDirectory::class);

        $manager->start($admin, $impersonated);

        $this->assertTrue($manager->isImpersonating());
        $this->assertAuthenticatedAs($impersonated);

        Livewire::actingAs($impersonated);

        $this->assertForbiddenLivewireCall($component, 'updateRole', [$target->id, UserRole::Subscriber->value]);

        $this->assertForbiddenLivewireCall($component, 'exportCsv');

        $component
            ->call('impersonate', $target->id)
            ->assertHasErrors(['impersonation']);

        $manager->stop();

        $this->assertFalse($manager->isImpersonating());
        $this->assertAuthenticatedAs($admin);

        $component = Livewire::actingAs($admin)->test(UserDirectory::class);

        $component->call('updateRole', $target->id, UserRole::Subscriber->value);

        $this->assertSame(UserRole::Subscriber, $target->fresh()->role);

        $response = $component->instance()->exportCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);

        $component
            ->call('impersonate', $impersonated->id);

        $this->assertTrue($manager->isImpersonating());
        $this->assertAuthenticatedAs($impersonated);

        $manager->stop();

        $this->assertAuthenticatedAs($admin);
    }

    private function assertForbiddenLivewireCall($component, string $method, array $parameters = []): void
    {
        $instance = $component->instance();
        $className = $instance ? $instance::class : UserDirectory::class;

        try {
            $component->instance()->{$method}(...$parameters);

            $this->fail(sprintf('Expected %s::%s to be forbidden.', $className, $method));
        } catch (AuthorizationException $exception) {
            $this->assertSame(403, $exception->status() ?? 403);
        }
    }
}
