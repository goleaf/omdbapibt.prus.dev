<?php

use App\Models\Movie;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        if (! Schema::hasColumn('reviews', 'movie_id')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->foreignId('movie_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained()
                    ->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('reviews', 'movie_title')) {
            $fallbackLocale = config('app.fallback_locale');

            DB::table('reviews')
                ->select('id', 'movie_title')
                ->orderBy('id')
                ->chunkById(200, function ($reviews) use ($fallbackLocale): void {
                    foreach ($reviews as $review) {
                        if (! is_string($review->movie_title) || $review->movie_title === '') {
                            continue;
                        }

                        $movieId = Movie::query()
                            ->where('title->en', $review->movie_title)
                            ->when($fallbackLocale, function ($query) use ($fallbackLocale, $review) {
                                $query->orWhere("title->$fallbackLocale", $review->movie_title);
                            })
                            ->orWhere('title', $review->movie_title)
                            ->value('id');

                        if (! $movieId) {
                            continue;
                        }

                        DB::table('reviews')
                            ->where('id', $review->id)
                            ->update(['movie_id' => $movieId]);
                    }
                });

            Schema::table('reviews', function (Blueprint $table): void {
                $table->dropColumn('movie_title');
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('reviews')) {
            return;
        }

        if (! Schema::hasColumn('reviews', 'movie_title')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->string('movie_title')->nullable()->after('user_id');
            });

            DB::table('reviews')
                ->select('id', 'movie_id')
                ->whereNotNull('movie_id')
                ->orderBy('id')
                ->chunkById(200, function ($reviews): void {
                    foreach ($reviews as $review) {
                        $movie = Movie::query()->find($review->movie_id);

                        if (! $movie) {
                            continue;
                        }

                        DB::table('reviews')
                            ->where('id', $review->id)
                            ->update(['movie_title' => $movie->localizedTitle('en')]);
                    }
                });
        }

        if (Schema::hasColumn('reviews', 'movie_id')) {
            Schema::table('reviews', function (Blueprint $table): void {
                $table->dropForeign(['movie_id']);
                $table->dropColumn('movie_id');
            });
        }
    }
};
