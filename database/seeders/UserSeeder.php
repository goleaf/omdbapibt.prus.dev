<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use SeedsModelsInChunks;

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

        $locales = $this->supportedLocales();

        $this->seedInChunks(self::TOTAL_USERS, self::CHUNK_SIZE, function (int $count) use ($locales): void {
            User::factory()
                ->count($count)
                ->state(fn (): array => [
                    'preferred_locale' => $locales[array_rand($locales)] ?? $this->fallbackLocale(),
                ])
                ->create();
        });

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
}
