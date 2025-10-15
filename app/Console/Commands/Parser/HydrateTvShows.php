<?php

namespace App\Console\Commands\Parser;

class HydrateTvShows extends ParserHydrationCommand
{
    protected $signature = 'parser:hydrate-tv '.self::OPTIONS;

    protected $description = 'Queue TV show records for parser hydration.';

    protected function targetKey(): string
    {
        return 'tv';
    }
}
