<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    private const TOTAL_PEOPLE = 1000;

    private const CHUNK_SIZE = 250;

    /**
     * Seed a catalog of people that can be attached to media credits.
     */
    public function run(): void
    {
        if (Person::query()->exists()) {
            return;
        }

        $remaining = self::TOTAL_PEOPLE;

        while ($remaining > 0) {
            $batchSize = min(self::CHUNK_SIZE, $remaining);

            Person::factory()
                ->count($batchSize)
                ->create();

            $remaining -= $batchSize;
        }
    }
}
