<?php

namespace Database\Seeders;

use App\Models\UiTranslation;
use Illuminate\Database\Seeder;

class UiTranslationSeeder extends Seeder
{
    private const TOTAL_TRANSLATIONS = 1000;

    private const CHUNK_SIZE = 250;

    /**
     * Seed translated UI labels for the demo environment.
     */
    public function run(): void
    {
        if (UiTranslation::query()->exists()) {
            return;
        }

        $remaining = self::TOTAL_TRANSLATIONS;

        while ($remaining > 0) {
            $batchSize = min(self::CHUNK_SIZE, $remaining);

            UiTranslation::factory()
                ->count($batchSize)
                ->create();

            $remaining -= $batchSize;
        }
    }
}
