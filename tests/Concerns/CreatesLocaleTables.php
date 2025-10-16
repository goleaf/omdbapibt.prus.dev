<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

trait CreatesLocaleTables
{
    protected function ensureLanguagesTable(): void
    {
        if (Schema::hasTable('languages')) {
            return;
        }

        Schema::create('languages', function (Blueprint $table): void {
            $table->id();
            $table->json('name_translations');
            $table->json('native_name_translations');
            $table->string('code', 5)->unique();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    protected function ensureCountriesTable(): void
    {
        if (Schema::hasTable('countries')) {
            return;
        }

        Schema::create('countries', function (Blueprint $table): void {
            $table->id();
            $table->string('code', 2)->unique();
            $table->json('name_translations');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    protected function ensureGenresTable(): void
    {
        if (Schema::hasTable('genres')) {
            return;
        }

        Schema::create('genres', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable();
            $table->string('slug')->unique();
            $table->json('name_translations');
            $table->timestamps();
        });
    }
}
