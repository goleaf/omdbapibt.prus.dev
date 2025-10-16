<?php

namespace Database\Seeders;

use App\Enums\UserManagementAction;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserManagementLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class UserManagementLogSeeder extends Seeder
{
    /**
     * Seed activity logs for administrative user management actions.
     */
    public function run(): void
    {
        if (UserManagementLog::query()->exists()) {
            return;
        }

        $actors = User::query()
            ->where('role', UserRole::Admin->value)
            ->get();

        $users = User::query()->get();

        if ($actors->isEmpty() || $users->count() < 2) {
            return;
        }

        $actors->each(function (User $actor) use ($users): void {
            $targets = $users->reject(fn (User $user): bool => $user->is($actor));

            if ($targets->isEmpty()) {
                return;
            }

            $logCount = random_int(2, 5);

            Collection::times($logCount, fn () => true)->each(function () use ($actor, $targets): void {
                $subject = $targets->random();
                $action = collect(UserManagementAction::cases())->random();

                $details = match ($action) {
                    UserManagementAction::ImpersonationStarted, UserManagementAction::ImpersonationStopped => [
                        'impersonated_user_id' => $subject->getKey(),
                    ],
                    UserManagementAction::RoleUpdated => [
                        'previous_role' => UserRole::User->value,
                        'new_role' => $subject->role instanceof UserRole ? $subject->role->value : $subject->role,
                    ],
                    UserManagementAction::QueuedCommand => [
                        'command' => 'users:notify',
                        'payload' => ['segments' => ['trialing', 'expired']],
                    ],
                };

                UserManagementLog::query()->create([
                    'actor_id' => $actor->getKey(),
                    'user_id' => $subject->getKey(),
                    'action' => $action->value,
                    'details' => $details,
                ]);
            });
        });
    }
}
