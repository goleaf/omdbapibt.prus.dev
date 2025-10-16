<?php

namespace Database\Factories;

use App\Models\OmdbApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<OmdbApiKey>
 */
class OmdbApiKeyFactory extends Factory
{
    protected $model = OmdbApiKey::class;

    public function definition(): array
    {
        return [
            'key' => Str::upper(Str::random(24)),
            'status' => $this->faker->randomElement([
                OmdbApiKey::STATUS_PENDING,
                OmdbApiKey::STATUS_VALID,
                OmdbApiKey::STATUS_INVALID,
                OmdbApiKey::STATUS_UNKNOWN,
            ]),
            'last_checked_at' => $this->faker->dateTimeBetween('-2 days', 'now'),
            'last_confirmed_at' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
        ];
    }

    public function pending(): self
    {
        return $this->state(fn (): array => ['status' => OmdbApiKey::STATUS_PENDING]);
    }

    public function valid(): self
    {
        return $this->state(fn (): array => ['status' => OmdbApiKey::STATUS_VALID]);
    }

    public function invalid(): self
    {
        return $this->state(fn (): array => ['status' => OmdbApiKey::STATUS_INVALID]);
    }

    public function unknown(): self
    {
        return $this->state(fn (): array => ['status' => OmdbApiKey::STATUS_UNKNOWN]);
    }

    public function withoutStatus(): self
    {
        return $this->state(fn (): array => ['status' => null]);
    }
}
