<?php

namespace App\Services\Parser\Hydrators;

use App\Models\Person;
use Illuminate\Database\Eloquent\Builder;

class PersonHydrator extends ModelHydrator
{
    public function __construct()
    {
        parent::__construct('people.hydrator');
    }

    protected function newQuery(): Builder
    {
        return Person::query()->with(['movies', 'tvShows']);
    }
}
