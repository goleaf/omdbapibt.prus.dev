<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed application users including administrative accounts.
     */
    public function run(): void
    {
        if (User::query()->exists()) {
            return;
        }

        User::factory()->count(8)->create();
        User::factory()->admin()->count(2)->create();
    }
}
