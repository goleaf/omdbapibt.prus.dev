<?php

namespace Database\Seeders;

use App\Models\OmdbApiKey;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class OmdbApiKeySeeder extends Seeder
{
    /**
     * Seed OMDB API key candidates with a representative mix of statuses.
     */
    public function run(): void
    {
        if (! Schema::hasTable('omdb_api_keys')) {
            return;
        }

        $this->ensureConfiguredKeyIsAvailable();

        $targets = [
            OmdbApiKey::STATUS_PENDING => 40,
            OmdbApiKey::STATUS_VALID => 10,
            OmdbApiKey::STATUS_INVALID => 5,
            OmdbApiKey::STATUS_UNKNOWN => 5,
            null => 5,
        ];

        foreach ($targets as $status => $targetCount) {
            $query = OmdbApiKey::query();

            if ($status === null) {
                $query->whereNull('status');
            } else {
                $query->where('status', $status);
            }

            $existingCount = (int) $query->count();
            $toCreate = max(0, $targetCount - $existingCount);

            if ($toCreate === 0) {
                continue;
            }

            $factory = OmdbApiKey::factory()->count($toCreate);

            $factory = match ($status) {
                OmdbApiKey::STATUS_PENDING => $factory->pending(),
                OmdbApiKey::STATUS_VALID => $factory->valid(),
                OmdbApiKey::STATUS_INVALID => $factory->invalid(),
                OmdbApiKey::STATUS_UNKNOWN => $factory->unknown(),
                default => $factory->withoutStatus(),
            };

            $factory->create();
        }
    }

    protected function ensureConfiguredKeyIsAvailable(): void
    {
        $configuredKey = (string) config('services.omdb.key', '');

        if ($configuredKey === '') {
            return;
        }

        OmdbApiKey::query()->updateOrCreate(
            ['key' => $configuredKey],
            [
                'status' => OmdbApiKey::STATUS_VALID,
                'first_seen_at' => now(),
                'last_checked_at' => now(),
                'last_confirmed_at' => now(),
            ],
        );
    }
}
