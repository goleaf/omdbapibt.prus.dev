<?php

namespace App\Jobs\Parsing;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExecuteParserPipeline implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The workload category this job represents.
     */
    public string $workload;

    private const QUEUE = 'parsing';

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    public function __construct(string $workload)
    {
        $this->workload = $workload;

        $this->onQueue(self::QUEUE);
    }

    /**
     * Determine the backoff (in seconds) for exponential retry attempts.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [60, 120, 240];
    }

    public function handle(): void
    {
        Log::info('Executing parser pipeline', [
            'workload' => $this->workload,
            'queue' => self::QUEUE,
        ]);
    }
}
