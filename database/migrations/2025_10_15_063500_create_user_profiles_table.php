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
        Schema::create('user_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('bio')->nullable();
            $table->foreignId('home_country_id')->nullable()->constrained('countries')->nullOnDelete();
            $table->foreignId('primary_genre_id')->nullable()->constrained('genres')->nullOnDelete();
            $table->foreignId('secondary_genre_id')->nullable()->constrained('genres')->nullOnDelete();
            $table->foreignId('favorite_movie_id')->nullable()->constrained('movies')->nullOnDelete();
            $table->foreignId('favorite_tv_show_id')->nullable()->constrained('tv_shows')->nullOnDelete();
            $table->foreignId('favorite_person_id')->nullable()->constrained('people')->nullOnDelete();
            $table->foreignId('primary_language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->foreignId('secondary_language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->foreignId('subtitle_language_id')->nullable()->constrained('languages')->nullOnDelete();
            $table->unsignedSmallInteger('weekly_watch_minutes')->default(0);
            $table->unsignedSmallInteger('average_session_minutes')->default(0);
            $table->unsignedTinyInteger('preferred_watch_hour')->nullable();
            $table->decimal('binge_watch_score', 5, 2)->default(0);
            $table->decimal('rewatch_affinity', 5, 2)->default(0);
            $table->timestamp('last_watched_at')->nullable();
            $table->json('recent_watch_highlights')->nullable();
            $table->timestamps();

            $table->unique('user_id');
            $table->index(['primary_genre_id', 'secondary_genre_id']);
            $table->index(['primary_language_id', 'secondary_language_id']);
            $table->index(['favorite_person_id']);
        });

        Schema::create('user_profile_genre_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('preference_rank')->default(1);
            $table->decimal('preference_score', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['user_profile_id', 'genre_id']);
            $table->index(['genre_id', 'preference_score']);
        });

        Schema::create('user_profile_language_preferences', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('preference_type', 32);
            $table->unsignedTinyInteger('preference_rank')->default(1);
            $table->timestamps();

            $table->unique(['user_profile_id', 'language_id', 'preference_type']);
            $table->index(['language_id', 'preference_rank']);
        });

        Schema::create('user_profile_person_favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_profile_id')->constrained()->cascadeOnDelete();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->unsignedTinyInteger('preference_rank')->default(1);
            $table->string('affinity_reason')->nullable();
            $table->timestamps();

            $table->unique(['user_profile_id', 'person_id']);
            $table->index(['person_id', 'preference_rank']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profile_person_favorites');
        Schema::dropIfExists('user_profile_language_preferences');
        Schema::dropIfExists('user_profile_genre_preferences');
        Schema::dropIfExists('user_profiles');
    }
};
