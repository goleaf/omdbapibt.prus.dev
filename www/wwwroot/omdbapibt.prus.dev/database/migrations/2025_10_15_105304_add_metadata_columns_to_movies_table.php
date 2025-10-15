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
        Schema::table('movies', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('video');
            $table->json('cast')->nullable()->after('translations');
            $table->json('crew')->nullable()->after('cast');
            $table->json('streaming_links')->nullable()->after('crew');
            $table->json('trailers')->nullable()->after('streaming_links');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->dropColumn([
                'translations',
                'cast',
                'crew',
                'streaming_links',
                'trailers',
            ]);
        });
    }
};
