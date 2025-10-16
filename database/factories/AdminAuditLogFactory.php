<?php

namespace Database\Factories;

use App\Enums\AdminAuditAction;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdminAuditLog>
 */
class AdminAuditLogFactory extends Factory
{
    protected $model = AdminAuditLog::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => $this->faker->randomElement(AdminAuditAction::cases())->value,
            'details' => [
                'ip_address' => $this->faker->ipv4(),
                'reason' => $this->faker->sentence(),
            ],
        ];
    }
}
