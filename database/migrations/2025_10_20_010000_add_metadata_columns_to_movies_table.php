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
        Schema::table('movies', function (Blueprint $table): void {
            $table->json('translation_metadata')->nullable()->after('overview');
            $table->json('credits')->nullable()->after('translation_metadata');
            $table->json('streaming_links')->nullable()->after('credits');
            $table->json('trailers')->nullable()->after('streaming_links');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table): void {
            $table->dropColumn(['translation_metadata', 'credits', 'streaming_links', 'trailers']);
        });
    }
};
