<?php

namespace App\Console\Commands;

use App\Models\Person;

class ParsePeopleCommand extends BaseParseCommand
{
    protected $signature = 'people:parse-new';

    protected $description = 'Parse the latest person data and persist it to storage';

    protected string $configKey = 'parser.people';

    protected string $modelClass = Person::class;
}
