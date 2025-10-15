<?php

use App\Jobs\Emails\SendSubscriptionDigest;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Jobs\System\WarmTrendingCache;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('movie:parse-new', function () {
    ExecuteParserPipeline::dispatch('movies')->onQueue('parsing');

    $this->info('Dispatched movie parsing pipeline.');
})->purpose('Dispatch a parsing workload for new movies');

Artisan::command('tv:parse-new', function () {
    ExecuteParserPipeline::dispatch('tv')->onQueue('parsing');

    $this->info('Dispatched TV parsing pipeline.');
})->purpose('Dispatch a parsing workload for new TV shows');

Artisan::command('people:parse-new', function () {
    ExecuteParserPipeline::dispatch('people')->onQueue('parsing');

    $this->info('Dispatched people parsing pipeline.');
})->purpose('Dispatch a parsing workload for new people profiles');

Artisan::command('cache:warm-trending', function () {
    WarmTrendingCache::dispatch()->onQueue('default');

    $this->info('Queued trending cache warmup.');
})->purpose('Prime caches for trending media lists');

Artisan::command('emails:send-digest', function () {
    $userId = User::query()->value('id') ?? 1;

    SendSubscriptionDigest::dispatch($userId)->onQueue('emails');

    $this->info('Queued subscription digest emails.');
})->purpose('Queue subscription digest emails for subscribers');
