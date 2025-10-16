<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    private const TOTAL_USERS = 1000;

    private const ADMIN_COUNT = 2;

    private const CHUNK_SIZE = 250;

    /**
     * Seed application users including administrative accounts.
     */
    public function run(): void
    {
        if (User::query()->exists()) {
            return;
        }

        $this->seedUsersInChunks(self::TOTAL_USERS);

        if (self::ADMIN_COUNT > 0) {
            User::query()
                ->orderBy('id')
                ->limit(self::ADMIN_COUNT)
                ->get()
                ->each(function (User $user): void {
                    $user->forceFill(['role' => UserRole::Admin->value])->save();
                });
        }
    }

    private function seedUsersInChunks(int $total): void
    {
        $remaining = $total;

        while ($remaining > 0) {
            $batchSize = min(self::CHUNK_SIZE, $remaining);

            User::factory()
                ->count($batchSize)
                ->create();

            $remaining -= $batchSize;
        }
    }
}
