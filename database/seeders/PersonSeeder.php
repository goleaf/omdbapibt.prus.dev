<?php

namespace Database\Seeders;

use App\Models\Person;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed a catalog of people that can be attached to media credits.
     */
    public function run(): void
    {
        if (Person::query()->exists()) {
            return;
        }

        $this->forChunkedCount(1_000, 250, function (int $count): void {
            Person::factory()->count($count)->create();
        });
    }
}
