<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserManagementLog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Cashier\Subscription;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserDirectory extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $search = '';

    #[Url(history: true)]
    public ?string $role = null;

    #[Url(history: true)]
    public ?string $plan = null;

    #[Url(history: true)]
    public int $perPage = 15;

    public array $perPageOptions = [10, 15, 25, 50, 100];

    protected $paginationTheme = 'tailwind';

    public function updating(string $name, $value): void
    {
        if (in_array($name, ['search', 'role', 'plan', 'perPage'], true)) {
            $this->resetPage();
        }
    }

    public function updatedPerPage($value): void
    {
        $size = (int) $value;

        if (! in_array($size, $this->perPageOptions, true)) {
            $this->perPage = 15;

            return;
        }

        $this->perPage = $size;
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return $this->usersQuery()
            ->with(['subscriptions' => fn ($query) => $query->orderByDesc('created_at')])
            ->paginate($this->perPage);
    }

    #[Computed]
    public function availablePlans(): array
    {
        return Subscription::query()
            ->select('name')
            ->distinct()
            ->orderBy('name')
            ->pluck('name')
            ->filter()
            ->values()
            ->all();
    }

    #[Computed]
    public function roleOptions(): array
    {
        return UserRole::options();
    }

    public function updateRole(int $userId, string $role): void
    {
        $admin = Auth::user();
        abort_unless($admin instanceof User && $admin->isAdmin(), 403);

        if (! in_array($role, UserRole::options(), true)) {
            $this->addError('role', __('The selected role is invalid.'));

            return;
        }

        if ($admin->id === $userId && $role !== UserRole::ADMIN->value) {
            $this->addError('role', __('Administrators cannot remove their own admin access.'));

            return;
        }

        $user = User::query()->findOrFail($userId);
        $previousRole = $user->role instanceof UserRole ? $user->role->value : $user->role;

        if ($previousRole === $role) {
            return;
        }

        $user->forceFill(['role' => $role])->save();

        $this->logAction($admin, $user, 'role_changed', [
            'previous_role' => $previousRole,
            'new_role' => $role,
        ]);

        $this->dispatch('notification', type: 'success', message: __('Role updated successfully.'));
    }

    public function impersonate(int $userId)
    {
        $admin = Auth::user();
        abort_unless($admin instanceof User && $admin->isAdmin(), 403);

        if ($admin->id === $userId) {
            $this->dispatch('notification', type: 'warning', message: __('You are already this user.'));

            return;
        }

        $user = User::query()->findOrFail($userId);

        session()->put('impersonator_id', $admin->id);
        session()->put('impersonator_role', $admin->role instanceof UserRole ? $admin->role->value : $admin->role);

        Auth::login($user);

        $this->logAction($admin, $user, 'impersonated', [
            'target_role' => $user->role instanceof UserRole ? $user->role->value : $user->role,
        ]);

        return redirect()->route('dashboard');
    }

    public function stopImpersonating()
    {
        $currentUser = Auth::user();
        $impersonatorId = session()->pull('impersonator_id');

        if (! $impersonatorId) {
            return;
        }

        $admin = User::query()->find($impersonatorId);
        $target = $currentUser instanceof User ? $currentUser : null;

        if (! $admin) {
            Auth::logout();

            return redirect()->route('login');
        }

        if ($target) {
            $this->logAction($admin, $target, 'impersonation_ended');
        }

        Auth::login($admin);
        session()->forget('impersonator_role');

        return redirect()->route('dashboard');
    }

    public function export(string $format = 'csv'): StreamedResponse
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $fileName = 'users-export-' . now()->format('Y-m-d_H-i-s') . '.' . $format;

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                __('Name'),
                __('Email'),
                __('Role'),
                __('Subscription Plan'),
                __('Subscription Status'),
            ]);

            $this->usersQuery()
                ->with('subscriptions')
                ->chunk(200, function ($users) use ($handle) {
                    foreach ($users as $user) {
                        $role = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;
                        $subscription = $user->currentSubscription();
                        fputcsv($handle, [
                            $user->name,
                            $user->email,
                            Str::headline($role),
                            $subscription?->name ?? __('None'),
                            $subscription?->stripe_status ?? __('N/A'),
                        ]);
                    }
                });

            fclose($handle);
        }, $fileName);
    }

    public function render()
    {
        return view('livewire.admin.user-directory');
    }

    private function usersQuery(): Builder
    {
        return User::query()
            ->withCount(['subscriptions as active_subscriptions_count' => function ($query) {
                $query->where('stripe_status', 'active');
            }])
            ->when($this->search !== '', function (Builder $query) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function (Builder $subQuery) use ($searchTerm) {
                    $subQuery
                        ->where('name', 'like', $searchTerm)
                        ->orWhere('email', 'like', $searchTerm);
                });
            })
            ->when($this->role !== null && $this->role !== '', function (Builder $query) {
                $query->where('role', $this->role);
            })
            ->when($this->plan !== null && $this->plan !== '', function (Builder $query) {
                if ($this->plan === 'none') {
                    $query->whereDoesntHave('subscriptions', function ($subQuery) {
                        $subQuery->whereNull('ends_at')->where('stripe_status', 'active');
                    });

                    return;
                }

                $plan = $this->plan;

                $query->whereHas('subscriptions', function ($subQuery) use ($plan) {
                    $subQuery->where('name', $plan);
                });
            })
            ->orderBy('name');
    }

    private function logAction(User $admin, User $target, string $action, array $metadata = []): void
    {
        UserManagementLog::create([
            'admin_id' => $admin->id,
            'target_user_id' => $target->id,
            'action' => $action,
            'metadata' => $metadata,
        ]);
    }
}
