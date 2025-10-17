<?php

namespace Database\Factories;

use App\Models\Platform;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * @extends Factory<Platform>
 */
class PlatformFactory extends Factory
{
    protected $model = Platform::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company().' '.$this->faker->randomElement(['TV', 'Streaming', 'Plus']);
        $slugBase = Str::slug($name);

        $attributes = [
            'name' => $name,
            'slug' => $slugBase.'-'.$this->faker->unique()->numerify('###'),
        ];

        if (Schema::hasColumn('platforms', 'is_active')) {
            $attributes['is_active'] = true;
        }

        if (Schema::hasColumn('platforms', 'type')) {
            $attributes['type'] = 'streaming';
        }

        if (Schema::hasColumn('platforms', 'website_url')) {
            $attributes['website_url'] = sprintf('https://%s.example.com', $slugBase);
        }

        if (Schema::hasColumn('platforms', 'name_translations')) {
            $attributes['name_translations'] = [
                'en' => $name,
            ];
        }

        if (Schema::hasColumn('platforms', 'metadata')) {
            $attributes['metadata'] = [
                'region_hints' => [$this->faker->countryCode()],
            ];
        }

        if (Schema::hasColumn('platforms', 'is_featured')) {
            $attributes['is_featured'] = false;
        }

        if (Schema::hasColumn('platforms', 'launch_country_id')) {
            $attributes['launch_country_id'] = null;
        }

        return $attributes;
    }
}
