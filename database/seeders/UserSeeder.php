<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;
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

        if (User::query()->exists()) {
            return;
        }

        $totalUsers = 1_000;

        $this->forChunkedCount($totalUsers, 250, function (int $count): void {
            User::factory()->count($count)->create();
        });

        User::query()
            ->orderBy('id')
            ->limit(2)
            ->get()
            ->each(function (User $user): void {
                $user->forceFill(['role' => UserRole::Admin])->save();
            });
    }
}
