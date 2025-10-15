<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Services\Parser\PersonUpsertService;

class ParsePeopleCommand extends ParserCommand
{
    protected $signature = 'parser:people';

    protected $description = 'Hydrate configured people into the local catalog.';

    protected string $configKey = 'people';

    protected string $modelLabel = 'person';

    public function __construct(protected PersonUpsertService $personUpsertService)
    {
        parent::__construct();
    }

    protected function hydrateRecord(array $payload): Person
    {
        return $this->personUpsertService->upsertPerson($payload);
    }
}
