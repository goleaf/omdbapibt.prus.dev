<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed application users including administrative accounts.
     */
    public function run(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $this->seedConfiguredAccounts();
        $this->ensureAdministrativeBaseline();

        $totalUsers = 1_000;
        $existingUsers = User::query()->count();
        $usersToCreate = max(0, $totalUsers - $existingUsers);

        if ($usersToCreate === 0) {
            return;
        }

        $this->forChunkedCount($usersToCreate, 250, function (int $count): void {
            User::factory()->withProfile()->count($count)->create();
        });
    }

    protected function seedConfiguredAccounts(): void
    {
        $accounts = [
            [
                'config' => config('seeding.accounts.admin', []),
                'role' => UserRole::Admin,
            ],
            [
                'config' => config('seeding.accounts.demo', []),
                'role' => UserRole::User,
            ],
        ];

        foreach ($accounts as $account) {
            $email = (string) ($account['config']['email'] ?? '');

            if ($email === '') {
                continue;
            }

            $name = (string) ($account['config']['name'] ?? '');

            if ($name === '') {
                $name = $account['role'] === UserRole::Admin ? 'Demo Administrator' : 'Demo Subscriber';
            }

            $preferredLocale = (string) ($account['config']['preferred_locale'] ?? 'en');
            $password = (string) ($account['config']['password'] ?? 'password');

            $user = User::query()->updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make($password),
                    'role' => $account['role'],
                    'preferred_locale' => $preferredLocale,
                ],
            );

            if ($user->email_verified_at === null) {
                $user->forceFill(['email_verified_at' => now()])->save();
            }

            // Ensure the user has a profile
            if (! $user->profile) {
                \App\Models\UserProfile::factory()->for($user)->create();
            }
        }
    }

    protected function ensureAdministrativeBaseline(): void
    {
        $minimumAdmins = 2;
        $currentAdmins = User::query()->where('role', UserRole::Admin)->count();

        $adminsToCreate = max(0, $minimumAdmins - $currentAdmins);

        if ($adminsToCreate === 0) {
            return;
        }

        User::factory()
            ->admin()
            ->withProfile()
            ->count($adminsToCreate)
            ->create();
    }
}
