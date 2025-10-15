<?php

namespace App\Jobs\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class WarmTrendingCache implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $queue = 'default';

    public int $tries = 3;

    public function __construct()
    {
        $this->onQueue($this->queue);
    }

    /**
     * Determine the backoff (in seconds) for exponential retry attempts.
     *
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [30, 90, 270];
    }

    public function handle(): void
    {
        Cache::set('trending:last_warm_run', now());
    }
}
