<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Platform>
 */
class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition(): array
    {
        $name = Str::title($this->faker->unique()->words(2, true));
        $slug = Str::slug($name).'-'.$this->faker->unique()->numerify('###');

        return [
            'slug' => $slug,
            'name' => $name,
            'url' => $this->faker->optional()->url(),
            'is_active' => true,
        ];
    }
}
