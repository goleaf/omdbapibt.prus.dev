<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Services\Parser\MediaUpsertService;

class ParseMoviesCommand extends ParserCommand
{
    protected $signature = 'parser:movies';

    protected $description = 'Hydrate configured movies into the local catalog.';

    protected string $configKey = 'movies';

    protected string $modelLabel = 'movie';

    public function __construct(protected MediaUpsertService $mediaUpsertService)
    {
        parent::__construct();
    }

    protected function hydrateRecord(array $payload): Movie
    {
        return $this->mediaUpsertService->upsertMovie($payload);
    }
}
