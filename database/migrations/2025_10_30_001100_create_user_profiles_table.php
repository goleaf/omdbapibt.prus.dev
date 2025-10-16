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
        Schema::create('user_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('display_name')->nullable();
            $table->string('tagline')->nullable();
            $table->text('bio')->nullable();
            $table->string('location')->nullable();
            $table->string('timezone')->nullable();
            $table->date('birthday')->nullable();
            $table->string('preferred_language')->nullable();
            $table->string('preferred_audio_language')->nullable();
            $table->string('preferred_subtitle_language')->nullable();
            $table->string('content_maturity')->nullable();
            $table->boolean('autoplay_next_episode')->default(true);
            $table->boolean('autoplay_trailers')->default(false);
            $table->boolean('newsletter_opt_in')->default(false);
            $table->boolean('marketing_opt_in')->default(false);
            $table->string('favorite_genre')->nullable();
            $table->string('favorite_movie')->nullable();
            $table->string('favorite_tv_show')->nullable();
            $table->string('favorite_actor')->nullable();
            $table->string('favorite_director')->nullable();
            $table->string('favorite_quote')->nullable();
            $table->string('website_url')->nullable();
            $table->string('twitter_url')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('tiktok_url')->nullable();
            $table->string('youtube_url')->nullable();
            $table->string('letterboxd_url')->nullable();
            $table->string('discord_handle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_profiles');
    }
};
