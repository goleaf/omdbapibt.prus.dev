<?php

namespace Database\Seeders;

use App\Models\UiTranslation;
use Illuminate\Database\Seeder;

class UiTranslationSeeder extends Seeder
{
    /**
     * Seed translated UI labels for the demo environment.
     */
    public function run(): void
    {
        if (UiTranslation::query()->exists()) {
            return;
        }

        UiTranslation::factory()->count(40)->create();
    }
}
