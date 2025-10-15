<?php

namespace App\Livewire\Admin;

use App\Models\AdminAuditLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Horizon\Horizon;
use Livewire\Component;

class HorizonMonitor extends Component
{
    public array $stats = [];

    public array $workload = [];

    public array $recentJobs = [];

    public array $auditLogEntries = [];

    public array $queuedCommands = [];

    public string $lastUpdated = '';

    public function mount(): void
    {
        if (! Auth::check() || ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->refresh();
    }

    public function refresh(): void
    {
        $this->loadHorizonData();
        $this->loadAuditLogs();
        $this->lastUpdated = now()->toDateTimeString();
    }

    public function queueCommand(string $command): void
    {
        if (! array_key_exists($command, $this->availableCommands())) {
            abort(403);
        }

        Artisan::queue($command);

        AdminAuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'queued_command',
            'details' => [
                'command' => $command,
            ],
        ]);

        $this->queuedCommands[] = [
            'command' => $command,
            'queued_at' => now()->toDateTimeString(),
        ];
        $this->queuedCommands = array_slice($this->queuedCommands, -20);

        $this->dispatch('parser-command-queued', command: $command);
        $this->refresh();
    }

    public function availableCommands(): array
    {
        return [
            'movie:parse-new' => 'Parse New Movies',
            'tv:parse-new' => 'Parse New TV Shows',
            'people:parse-new' => 'Parse New People',
        ];
    }

    public function render(): View
    {
        return view('livewire.admin.horizon-monitor', [
            'commands' => $this->availableCommands(),
        ]);
    }

    protected function loadHorizonData(): void
    {
        try {
            $this->stats = Horizon::stats()->toArray();
            $this->workload = collect(Horizon::workload())
                ->map(fn (array $queue) => [
                    'queue' => $queue['queue'] ?? 'default',
                    'length' => $queue['length'] ?? 0,
                    'wait' => $queue['wait'] ?? 0,
                ])
                ->values()
                ->all();
            $this->recentJobs = collect(Horizon::recentJobs())
                ->take(10)
                ->map(fn (array $job) => [
                    'id' => $job['id'] ?? null,
                    'name' => $job['name'] ?? $job['displayName'] ?? 'Unknown Job',
                    'queue' => $job['queue'] ?? 'default',
                    'status' => Str::headline($job['status'] ?? 'pending'),
                    'completed_at' => $job['completed_at'] ?? null,
                    'failed_at' => $job['failed_at'] ?? null,
                ])
                ->all();
        } catch (\Throwable) {
            $this->stats = [];
            $this->workload = [];
            $this->recentJobs = [];
        }
    }

    protected function loadAuditLogs(): void
    {
        $this->auditLogEntries = AdminAuditLog::query()
            ->with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (AdminAuditLog $log) => [
                'id' => $log->id,
                'action' => Str::headline($log->action),
                'details' => $log->details,
                'created_at' => optional($log->created_at)->toDateTimeString(),
                'user' => $log->user ? [
                    'name' => $log->user->name,
                    'email' => $log->user->email,
                ] : null,
            ])
            ->all();
    }
}
