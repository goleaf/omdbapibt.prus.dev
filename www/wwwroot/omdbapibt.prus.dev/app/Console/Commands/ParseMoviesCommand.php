<?php

namespace App\Console\Commands;

use App\Models\Movie;

class ParseMoviesCommand extends BaseParseCommand
{
    protected $signature = 'movie:parse-new';

    protected $description = 'Parse the latest movie data and persist it to storage';

    protected string $configKey = 'parser.movies';

    protected string $modelClass = Movie::class;
}
