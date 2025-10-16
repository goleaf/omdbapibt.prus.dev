<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Seed a catalog of people that can be attached to media credits.
     */
    public function run(): void
    {
        if (Person::query()->exists()) {
            return;
        }

        Person::factory()->count(40)->create();
    }
}
