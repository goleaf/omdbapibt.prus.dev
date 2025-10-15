<?php

use App\Enums\ParserWorkload;
use App\Jobs\Emails\SendSubscriptionDigest;
use App\Jobs\Parsing\ExecuteParserPipeline;
use App\Jobs\System\WarmTrendingCache;
use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('movie:parse-new', function () {
    ExecuteParserPipeline::dispatch(ParserWorkload::Movies);

    $this->info('Dispatched movie parsing pipeline.');
})->purpose('Dispatch a parsing workload for new movies');

Artisan::command('tv:parse-new', function () {
    ExecuteParserPipeline::dispatch(ParserWorkload::Tv);

    $this->info('Dispatched TV parsing pipeline.');
})->purpose('Dispatch a parsing workload for new TV shows');

Artisan::command('people:parse-new', function () {
    ExecuteParserPipeline::dispatch(ParserWorkload::People);

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

Artisan::command('db:optimize', function () {
    try {
        DB::statement('PRAGMA optimize');
        DB::statement('PRAGMA wal_checkpoint(TRUNCATE)');
        $this->info('SQLite optimize + WAL checkpoint done.');
    } catch (\Throwable $e) {
        $this->error('DB optimize failed: '.$e->getMessage());
    }
})->purpose('Run SQLite optimize and WAL checkpoint');
