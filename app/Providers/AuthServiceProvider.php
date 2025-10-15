<?php

namespace App\Providers;

use App\Models\Movie;
use App\Models\ParserEntry;
use App\Policies\MoviePolicy;
use App\Policies\ParserEntryPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Movie::class => MoviePolicy::class,
        ParserEntry::class => ParserEntryPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
