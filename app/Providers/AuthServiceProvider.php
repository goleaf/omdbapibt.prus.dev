<?php

namespace App\Providers;

use App\Models\Country;
use App\Models\Genre;
use App\Models\Language;
use App\Models\Movie;
use App\Models\ParserEntry;
use App\Models\Person;
use App\Models\TvShow;
use App\Models\UiTranslation;
use App\Models\User;
use App\Policies\AdminCrudPolicy;
use App\Policies\MoviePolicy;
use App\Policies\ParserEntryPolicy;
use App\Policies\UiTranslationPolicy;
use App\Policies\UserPolicy;
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
        TvShow::class => AdminCrudPolicy::class,
        Person::class => AdminCrudPolicy::class,
        Genre::class => AdminCrudPolicy::class,
        Language::class => AdminCrudPolicy::class,
        Country::class => AdminCrudPolicy::class,
        ParserEntry::class => ParserEntryPolicy::class,
        UiTranslation::class => UiTranslationPolicy::class,
        User::class => UserPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
