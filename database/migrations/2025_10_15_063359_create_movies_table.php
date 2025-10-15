<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $connection = Schema::getConnection()->getDriverName();

        Schema::create('movies', function (Blueprint $table) use ($connection) {
            $table->id();
            $table->unsignedBigInteger('tmdb_id')->nullable();
            $table->string('imdb_id', 20)->nullable();
            $table->string('omdb_id', 50)->nullable()->index();
            $table->string('slug')->nullable();
            $table->json('title');
            $table->string('original_title')->nullable();
            $table->json('overview')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->unsignedSmallInteger('runtime')->nullable();
            $table->date('release_date')->nullable();
            $table->text('plot')->nullable();
            $table->string('tagline')->nullable();
            $table->string('homepage')->nullable();
            $table->unsignedBigInteger('budget')->nullable();
            $table->unsignedBigInteger('revenue')->nullable();
            $table->string('status')->nullable();
            $table->decimal('popularity', 10, 3)->nullable();
            $table->decimal('vote_average', 3, 1)->nullable();
            $table->unsignedInteger('vote_count')->nullable();
            $table->string('poster_path')->nullable();
            $table->string('backdrop_path')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('media_type', 50)->nullable();
            $table->boolean('adult')->default(false);
            $table->boolean('video')->default(false);
            $table->timestamps();
            $table->softDeletes();

            if ($connection !== 'sqlite') {
                $table->text('title_search_vector')->storedAs(<<<'SQL'
trim(
    concat_ws(
        ' ',
        json_unquote(json_extract(`title`, '$."en"')),
        json_unquote(json_extract(`title`, '$."es"')),
        json_unquote(json_extract(`title`, '$."fr"'))
    )
)
SQL);

                $table->text('overview_search_vector')->storedAs(<<<'SQL'
trim(
    concat_ws(
        ' ',
        json_unquote(json_extract(`overview`, '$."en"')),
        json_unquote(json_extract(`overview`, '$."es"')),
        json_unquote(json_extract(`overview`, '$."fr"'))
    )
)
SQL);
            } else {
                $table->text('title_search_vector')->nullable();
                $table->text('overview_search_vector')->nullable();
            }

            $table->index('tmdb_id');
            $table->index('imdb_id');
            $table->index('slug');
            $table->index('popularity');
            $table->index('vote_average');
            $table->unique('tmdb_id');
            $table->unique('imdb_id');
            $table->unique('slug');

            if ($connection !== 'sqlite') {
                $table->fullText(['title_search_vector', 'overview_search_vector'], 'movies_fulltext_translations');
            }
        });

        if ($this->usingMySql()) {
            Schema::table('movies', function (Blueprint $table) {
                $table->fullText('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }

    private function usingMySql(): bool
    {
        return Schema::getConnection()->getDriverName() === 'mysql';
    }
};
