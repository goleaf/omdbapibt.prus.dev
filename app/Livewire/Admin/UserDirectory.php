<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserManagementLog;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserDirectory extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $role = 'all';

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $sort = 'latest';

    protected $listeners = [
        'user-role-updated' => '$refresh',
    ];

    public function mount(): void
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->role = 'all';
        $this->status = 'all';
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function getUsersProperty(): LengthAwarePaginator
    {
        return $this->filteredUsersQuery()
            ->paginate(15)
            ->withQueryString();
    }

    public function getRecentLogsProperty(): array
    {
        return UserManagementLog::query()
            ->with(['admin', 'target'])
            ->latest('performed_at')
            ->limit(10)
            ->get()
            ->map(fn (UserManagementLog $log) => [
                'id' => $log->id,
                'action' => $log->action,
                'performed_at' => $log->performed_at?->toDateTimeString(),
                'admin' => $log->admin?->only(['id', 'name', 'email']),
                'target' => $log->target?->only(['id', 'name', 'email']),
                'payload' => $log->payload ?? [],
            ])
            ->all();
    }

    public function updateUserRole(int $userId, string $role): void
    {
        $admin = Auth::user();

        if (! $admin || ! $admin->isAdmin()) {
            abort(403);
        }

        $newRole = UserRole::tryFrom($role);

        if (! $newRole) {
            $this->addError('role', 'Invalid role selection.');

            return;
        }

        $user = User::query()->findOrFail($userId);

        if ($admin->is($user) && $newRole !== UserRole::Admin) {
            $this->addError('role', 'You cannot remove your own administrator role.');

            return;
        }

        if ($user->role === $newRole) {
            return;
        }

        DB::transaction(function () use ($admin, $user, $newRole): void {
            $previousRole = $user->role;
            $user->forceFill(['role' => $newRole])->save();

            UserManagementLog::record($admin, $user, 'role_updated', [
                'previous_role' => $previousRole?->value,
                'new_role' => $newRole->value,
            ]);
        });

        $this->dispatch('user-role-updated', id: $user->id);
        $this->resetErrorBag('role');
    }

    public function impersonate(int $userId): void
    {
        $admin = Auth::user();

        if (! $admin || ! $admin->isAdmin()) {
            abort(403);
        }

        if ($admin->getKey() === $userId) {
            $this->addError('impersonate', 'You cannot impersonate your own account.');

            return;
        }

        $user = User::query()->findOrFail($userId);

        if (! $user->canBeImpersonated()) {
            $this->addError('impersonate', 'This account is not eligible for impersonation.');

            return;
        }

        session()->put(User::IMPERSONATOR_SESSION_KEY, $admin->getKey());
        Auth::login($user);

        UserManagementLog::record($admin, $user, 'impersonation_started', [
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        $this->redirectRoute('dashboard');
    }

    public function export(): StreamedResponse
    {
        $admin = Auth::user();

        if (! $admin || ! $admin->isAdmin()) {
            abort(403);
        }

        $filename = 'user-directory-'.now()->format('Ymd_His').'.csv';

        UserManagementLog::record($admin, null, 'directory_exported', [
            'filters' => [
                'search' => $this->search,
                'role' => $this->role,
                'status' => $this->status,
                'sort' => $this->sort,
            ],
        ]);

        $query = $this->filteredUsersQuery();

        return response()->streamDownload(function () use ($query): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, [
                'ID',
                'Name',
                'Email',
                'Role',
                'Email Verified At',
                'Created At',
            ]);

            $exportQuery = clone $query;

            $exportQuery->lazy(200)->each(function (User $user) use ($handle): void {
                fputcsv($handle, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role instanceof UserRole ? $user->role->value : $user->role,
                    optional($user->email_verified_at)?->toDateTimeString(),
                    optional($user->created_at)?->toDateTimeString(),
                ]);
            });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function render(): View
    {
        return view('livewire.admin.user-directory', [
            'users' => $this->users,
            'roleOptions' => UserRole::options(),
            'recentLogs' => $this->recentLogs,
        ]);
    }

    protected function filteredUsersQuery(): Builder
    {
        $query = User::query()->orderBy('created_at', $this->sort === 'oldest' ? 'asc' : 'desc');

        $searchTerm = trim($this->search);

        if ($searchTerm !== '') {
            $query->where(function (Builder $builder) use ($searchTerm): void {
                $builder
                    ->where('name', 'like', '%'.$searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$searchTerm.'%');
            });
        }

        if ($this->role !== 'all') {
            $role = UserRole::tryFrom($this->role);

            if ($role) {
                $query->where('role', $role->value);
            }
        }

        if ($this->status === 'verified') {
            $query->whereNotNull('email_verified_at');
        } elseif ($this->status === 'unverified') {
            $query->whereNull('email_verified_at');
        }

        return $query;
    }
}
