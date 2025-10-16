<?php

namespace Database\Seeders;

use App\Models\Person;
use Database\Seeders\Concerns\HandlesSeederChunks;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PersonSeeder extends Seeder
{
    use HandlesSeederChunks;

    /**
     * Seed a catalog of people that can be attached to media credits.
     */
    public function run(): void
    {
        if (! Schema::hasTable('people')) {
            return;
        }

        $target = 1_000;
        $existing = Person::query()->count();
        $remaining = max(0, $target - $existing);

        if ($remaining === 0) {
            return;
        }

        $this->forChunkedCount($remaining, 250, function (int $count): void {
            Person::factory()->count($count)->create();
        });
    }
}
