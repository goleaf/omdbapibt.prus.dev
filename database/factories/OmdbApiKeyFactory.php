<?php

namespace Database\Factories;

use App\Models\OmdbApiKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OmdbApiKey>
 */
class OmdbApiKeyFactory extends Factory
{
    protected $model = OmdbApiKey::class;

    public function definition(): array
    {
        // Generate 8-character alphanumeric key (0-9a-z)
        $charset = '0123456789abcdefghijklmnopqrstuvwxyz';
        $key = '';
        for ($i = 0; $i < 8; $i++) {
            $key .= $charset[random_int(0, strlen($charset) - 1)];
        }

        return [
            'key' => $key,
            'status' => $this->faker->randomElement([
                OmdbApiKey::STATUS_PENDING,
                OmdbApiKey::STATUS_VALID,
                OmdbApiKey::STATUS_INVALID,
                OmdbApiKey::STATUS_UNKNOWN,
            ]),
            'first_seen_at' => $this->faker->optional()->dateTimeBetween('-7 days', 'now'),
            'last_checked_at' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
            'last_confirmed_at' => $this->faker->optional()->dateTimeBetween('-2 days', 'now'),
            'last_response_code' => $this->faker->optional()->randomElement([200, 401, 500]),
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
