<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Livewire\Admin\UserDirectory;
use App\Models\User;
use App\Support\ImpersonationManager;
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
}
