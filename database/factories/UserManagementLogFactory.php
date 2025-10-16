<?php

namespace Database\Factories;

use App\Enums\UserManagementAction;
use App\Models\User;
use App\Models\UserManagementLog;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserManagementLog>
 */
class UserManagementLogFactory extends Factory
{
    protected $model = UserManagementLog::class;

    public function definition(): array
    {
        return [
            'actor_id' => User::factory(),
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(UserManagementAction::cases())->value,
            'details' => [
                'summary' => $this->faker->sentence(),
                'metadata' => [
                    'ip_address' => $this->faker->ipv4(),
                ],
            ],
        ];
    }
}
