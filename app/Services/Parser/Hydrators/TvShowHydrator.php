<?php

namespace App\Services\Parser\Hydrators;

use App\Models\TvShow;
use Illuminate\Database\Eloquent\Builder;

class TvShowHydrator extends ModelHydrator
{
    public function __construct()
    {
        parent::__construct('tv.hydrator');
    }

    protected function newQuery(): Builder
    {
        return TvShow::query()->with(['people']);
    }
}
