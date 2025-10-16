<?php

namespace App\Livewire\Admin;

use App\Enums\UserManagementAction;
use App\Enums\UserRole;
use App\Models\User;
use App\Models\UserManagementLog;
use App\Support\ImpersonationManager;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserDirectory extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $roleFilter = null;

    public int $perPage = 15;

    protected ImpersonationManager $impersonationManager;

    public function boot(ImpersonationManager $impersonationManager): void
    {
        $this->impersonationManager = $impersonationManager;
    }

    public function mount(): void
    {
        $this->authorize('viewAny', User::class);
    }

    #[Computed]
    public function roles(): array
    {
        return collect(UserRole::cases())
            ->mapWithKeys(fn (UserRole $role) => [$role->value => $role->label()])
            ->all();
    }

    #[Computed]
    public function users(): LengthAwarePaginator
    {
        return $this->query()->paginate($this->perPage);
    }

    #[Computed]
    public function impersonating(): bool
    {
        return $this->impersonationManager->isImpersonating();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingRoleFilter(): void
    {
        $this->resetPage();
    }

    public function updateRole(int $userId, string $roleValue): void
    {
        $role = UserRole::tryFrom($roleValue);

        if (! $role) {
            $this->addError('role', 'Invalid role provided.');

            return;
        }

        /** @var User|null $user */
        $user = User::query()->find($userId);

        if (! $user) {
            return;
        }

        $this->authorize('updateRole', $user);

        $user->role = $role;
        $user->save();

        UserManagementLog::create([
            'actor_id' => auth()->id(),
            'user_id' => $user->getKey(),
            'action' => UserManagementAction::RoleUpdated,
            'details' => [
                'role' => $role->value,
            ],
        ]);

        $this->dispatch('user-role-updated');
    }

    public function exportCsv(): StreamedResponse
    {
        $this->authorize('export', User::class);

        $filename = 'user-directory-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'wb');

            fputcsv($handle, ['Name', 'Email', 'Role', 'Watch Events', 'Joined']);

            $this->query()
                ->cursor()
                ->each(function (User $user) use ($handle): void {
                    fputcsv($handle, [
                        $user->name,
                        $user->email,
                        $user->roleLabel(),
                        (string) $user->watch_histories_count,
                        optional($user->created_at)->toDateTimeString(),
                    ]);
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    protected function query(): Builder
    {
        return User::query()
            ->withCount(['watchHistories'])
            ->when($this->search !== '', function (Builder $query): void {
                $query->where(function (Builder $inner): void {
                    $term = '%'.Str::of($this->search)->trim().'%';
                    $inner
                        ->where('name', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->when($this->roleFilter, fn (Builder $query, string $role): Builder => $query->where('role', $role))
            ->orderByDesc('created_at');
    }

    public function impersonate(int $userId): void
    {
        if ($this->impersonationManager->isImpersonating()) {
            $this->addError('impersonation', 'You are already impersonating another user.');

            return;
        }

        /** @var User|null $target */
        $target = User::query()->find($userId);

        if (! $target) {
            $this->addError('impersonation', 'Unable to impersonate this user.');

            return;
        }

        $this->authorize('impersonate', $target);

        $actor = auth()->user();

        if (! $actor instanceof User) {
            $this->addError('impersonation', 'Unable to impersonate this user.');

            return;
        }

        $this->impersonationManager->start($actor, $target);
    }

    public function render(): View
    {
        return view('livewire.admin.user-directory');
    }
}
