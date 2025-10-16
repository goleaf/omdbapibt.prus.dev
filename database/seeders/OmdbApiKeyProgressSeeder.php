<?php

namespace Database\Seeders;

use App\Models\OmdbApiKeyProgress;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class OmdbApiKeyProgressSeeder extends Seeder
{
    /**
     * Seed historical checkpoints for the OMDB key brute-force process.
     */
    public function run(): void
    {
        if (! Schema::hasTable('omdb_api_key_progress')) {
            return;
        }

        $milestones = collect(['0', '250000', '500000']);

        $milestones->each(function (string $cursor): void {
            if (OmdbApiKeyProgress::query()->where('sequence_cursor', $cursor)->exists()) {
                return;
            }

            OmdbApiKeyProgress::factory()->create(['sequence_cursor' => $cursor]);
        });

        $targetCount = 5;
        $existingCount = (int) OmdbApiKeyProgress::query()->count();
        $toCreate = max(0, $targetCount - $existingCount);

        if ($toCreate === 0) {
            return;
        }

        OmdbApiKeyProgress::factory()->count($toCreate)->create();
    }
}
