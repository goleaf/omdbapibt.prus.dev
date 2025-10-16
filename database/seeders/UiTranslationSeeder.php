<?php

namespace Database\Seeders;

use App\Models\UiTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class UiTranslationSeeder extends Seeder
{
    /**
     * Seed translated UI labels for the demo environment.
     */
    public function run(): void
    {
        if (! Schema::hasTable('ui_translations')) {
            return;
        }

        $target = 1_000;
        $existing = UiTranslation::query()->count();
        $remaining = max(0, $target - $existing);

        if ($remaining === 0) {
            return;
        }

        UiTranslation::factory()->count($remaining)->create();
    }
}
