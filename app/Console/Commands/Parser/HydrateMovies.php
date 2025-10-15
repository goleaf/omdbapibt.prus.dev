<?php

namespace App\Console\Commands\Parser;

class HydrateMovies extends ParserHydrationCommand
{
    protected $signature = 'parser:hydrate-movies '.self::OPTIONS;

    protected $description = 'Queue movie records for parser hydration.';

    protected function targetKey(): string
    {
        return 'movies';
    }
}
