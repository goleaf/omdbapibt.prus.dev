<?php

namespace App\Console\Commands;

use App\Models\TvShow;

class ParseTvShowsCommand extends BaseParseCommand
{
    protected $signature = 'tv:parse-new';

    protected $description = 'Parse the latest TV show data and persist it to storage';

    protected string $configKey = 'parser.tv_shows';

    protected string $modelClass = TvShow::class;
}
