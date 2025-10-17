<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        $label = Str::title($this->faker->unique()->words(2, true));
        $slug = Str::slug($label);

        return [
            'slug' => $slug.'-'.$this->faker->unique()->numerify('###'),
            'name_i18n' => [
                'en' => $label,
                'es' => $label.' ES',
                'fr' => $label.' FR',
            ],
            'type' => $this->faker->randomElement(Tag::TYPES),
        ];
    }
}
