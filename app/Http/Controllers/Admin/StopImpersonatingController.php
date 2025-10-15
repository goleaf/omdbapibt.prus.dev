<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserManagementLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StopImpersonatingController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $impersonatorId = User::impersonatorId();
        $impersonatedUser = $request->user();

        User::clearImpersonation();

        if (! $impersonatorId) {
            return redirect()->route('dashboard');
        }

        $impersonator = User::query()->find($impersonatorId);

        if ($impersonator) {
            Auth::login($impersonator);

            if ($impersonator->isAdmin()) {
                UserManagementLog::record($impersonator, $impersonatedUser, 'impersonation_ended', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }

            return redirect()->route('admin.users');
        }

        Auth::logout();

        return redirect()->route('login');
    }
}
