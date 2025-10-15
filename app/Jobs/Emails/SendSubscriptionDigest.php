<?php

namespace App\Jobs\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSubscriptionDigest implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public string $queue = 'emails';

    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public readonly int $userId)
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
        return [120, 240, 480];
    }

    public function handle(): void
    {
        Log::info('Sending subscription digest email', [
            'user_id' => $this->userId,
            'queue' => $this->queue,
        ]);
    }
}
