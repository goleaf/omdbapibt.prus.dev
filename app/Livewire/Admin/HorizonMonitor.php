<?php

namespace App\Livewire\Admin;

use App\Enums\UserManagementAction;
use App\Models\UserManagementLog;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Horizon\Contracts\JobRepository;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Laravel\Horizon\Contracts\MetricsRepository;
use Laravel\Horizon\Contracts\SupervisorRepository;
use Laravel\Horizon\Contracts\WorkloadRepository;
use Laravel\Horizon\WaitTimeCalculator;
use Livewire\Component;
use Throwable;

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

        UserManagementLog::create([
            'user_id' => Auth::id(),
            'actor_id' => Auth::id(),
            'action' => UserManagementAction::QueuedCommand,
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
            $jobRepository = app(JobRepository::class);
            $metricsRepository = app(MetricsRepository::class);
            $workloadRepository = app(WorkloadRepository::class);
            $masterSupervisors = app(MasterSupervisorRepository::class);
            $supervisors = app(SupervisorRepository::class);
            $waitTimes = app(WaitTimeCalculator::class);

            $masters = collect($masterSupervisors->all() ?? []);

            $status = 'inactive';

            if ($masters->isNotEmpty()) {
                $status = $masters->every(fn ($master) => ($master->status ?? null) === 'paused')
                    ? 'paused'
                    : 'running';
            }

            $this->stats = [
                'jobsPerMinute' => $metricsRepository->jobsProcessedPerMinute(),
                'jobsProcessed' => $jobRepository->countCompleted(),
                'jobsFailed' => $jobRepository->countRecentlyFailed(),
                'status' => Str::headline($status),
                'longestWait' => collect($waitTimes->calculate())
                    ->take(1)
                    ->map(fn (int $seconds, string $queue) => [
                        'queue' => $queue,
                        'seconds' => $seconds,
                    ])
                    ->first(),
                'activeWorkers' => collect($supervisors->all() ?? [])
                    ->reduce(fn (int $carry, $supervisor) => $carry + (int) collect($supervisor->processes ?? [])->sum(), 0),
            ];

            $this->workload = collect($workloadRepository->get() ?? [])
                ->map(fn (array $queue) => [
                    'queue' => $queue['name'] ?? $queue['queue'] ?? 'default',
                    'length' => $queue['length'] ?? 0,
                    'wait' => $queue['wait'] ?? 0,
                    'processes' => $queue['processes'] ?? 0,
                ])
                ->values()
                ->all();

            $this->recentJobs = $jobRepository->getRecent()
                ->take(10)
                ->map(function ($job) {
                    $payload = [];

                    if (! empty($job->payload)) {
                        $payload = json_decode($job->payload, true) ?: [];
                    }

                    return [
                        'id' => $job->id ?? null,
                        'name' => $job->name ?? data_get($payload, 'displayName', 'Unknown Job'),
                        'queue' => $job->queue ?? 'default',
                        'status' => Str::headline($job->status ?? 'pending'),
                        'completed_at' => $job->completed_at ?? null,
                        'failed_at' => $job->failed_at ?? null,
                    ];
                })
                ->all();
        } catch (Throwable) {
            $this->stats = [];
            $this->workload = [];
            $this->recentJobs = [];
        }
    }

    protected function loadAuditLogs(): void
    {
        $this->auditLogEntries = UserManagementLog::query()
            ->with(['user', 'actor'])
            ->latest()
            ->take(10)
            ->get()
            ->map(fn (UserManagementLog $log) => [
                'id' => $log->id,
                'action' => Str::headline($log->action?->value ?? ''),
                'details' => $log->details,
                'created_at' => optional($log->created_at)->toDateTimeString(),
                'user' => $log->user ? [
                    'name' => $log->user->name,
                    'email' => $log->user->email,
                ] : null,
                'actor' => $log->actor ? [
                    'name' => $log->actor->name,
                    'email' => $log->actor->email,
                ] : null,
            ])
            ->all();
    }
}
