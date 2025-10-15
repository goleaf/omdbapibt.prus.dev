<?php

namespace App\Console\Commands;

use App\Models\TvShow;
use App\Services\Parser\MediaUpsertService;

class ParseTvShowsCommand extends ParserCommand
{
    protected $signature = 'parser:tv-shows';

    protected $description = 'Hydrate configured TV shows into the local catalog.';

    protected string $configKey = 'tv_shows';

    protected string $modelLabel = 'TV show';

    public function __construct(protected MediaUpsertService $mediaUpsertService)
    {
        parent::__construct();
    }

    protected function hydrateRecord(array $payload): TvShow
    {
        return $this->mediaUpsertService->upsertTvShow($payload);
    }
}
