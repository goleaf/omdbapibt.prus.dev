<?php

namespace App\Services\Parser\Hydrators;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Builder;

class MovieHydrator extends ModelHydrator
{
    public function __construct()
    {
        parent::__construct('movies.hydrator');
    }

    protected function newQuery(): Builder
    {
        return Movie::query()->with(['genres', 'people']);
    }
}
