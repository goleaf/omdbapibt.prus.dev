<?php

namespace App\Console\Commands\Parser;

class HydratePeople extends ParserHydrationCommand
{
    protected $signature = 'parser:hydrate-people '.self::OPTIONS;

    protected $description = 'Queue people records for parser hydration.';

    protected function targetKey(): string
    {
        return 'people';
    }
}
