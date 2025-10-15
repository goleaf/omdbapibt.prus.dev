<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Livewire\Admin\UserDirectory;
use App\Models\User;
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

    public function test_export_csv_streams_all_filtered_rows(): void
    {
        $admin = User::factory()->admin()->create();
        $users = User::factory()->count(20)->create();

        $component = Livewire::actingAs($admin)
            ->test(UserDirectory::class)
            ->set('roleFilter', UserRole::User->value);

        /** @var StreamedResponse $response */
        $response = $component->instance()->exportCsv();

        $this->assertInstanceOf(StreamedResponse::class, $response);

        ob_start();
        try {
            $response->sendContent();
        } finally {
            $csv = ob_get_clean();
        }

        foreach ($users as $user) {
            $this->assertStringContainsString($user->email, $csv);
        }

        $rows = array_filter(array_map('trim', explode("\n", trim((string) $csv))));

        $this->assertCount($users->count() + 1, $rows); // header + users
    }

    public function test_non_admin_cannot_access_directory(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get(route('admin.users'))->assertForbidden();
    }
}
