<?php

namespace App\Support;

use App\Enums\UserManagementAction;
use App\Models\User;
use App\Models\UserManagementLog;
use Illuminate\Contracts\Auth\StatefulGuard;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;

class ImpersonationManager
{
    public const SESSION_KEY = 'impersonator_id';

    public function __construct(private Session $session) {}

    public function start(User $actor, User $target): void
    {
        if (! $actor->canImpersonate() || ! $target->canBeImpersonated()) {
            abort(403, 'Impersonation is not permitted for this account.');
        }

        $this->session->put(self::SESSION_KEY, $actor->getKey());
        $this->guard()->login($target);

        UserManagementLog::create([
            'actor_id' => $actor->getKey(),
            'user_id' => $target->getKey(),
            'action' => UserManagementAction::ImpersonationStarted,
            'details' => [
                'actor_email' => $actor->email,
                'target_email' => $target->email,
            ],
        ]);
    }

    public function stop(): void
    {
        $impersonatorId = $this->session->pull(self::SESSION_KEY);

        if (! $impersonatorId) {
            return;
        }

        /** @var User|null $impersonator */
        $impersonator = User::find($impersonatorId);

        if ($impersonator) {
            $this->guard()->login($impersonator);

            UserManagementLog::create([
                'actor_id' => $impersonatorId,
                'user_id' => $impersonatorId,
                'action' => UserManagementAction::ImpersonationStopped,
                'details' => null,
            ]);
        } else {
            $this->guard()->logout();
        }
    }

    public function impersonator(): ?User
    {
        $impersonatorId = $this->session->get(self::SESSION_KEY);

        if (! $impersonatorId) {
            return null;
        }

        /** @var User|null $impersonator */
        $impersonator = User::find($impersonatorId);

        return $impersonator;
    }

    public function isImpersonating(): bool
    {
        return $this->session->has(self::SESSION_KEY);
    }

    protected function guard(): StatefulGuard
    {
        return Auth::guard();
    }
}
