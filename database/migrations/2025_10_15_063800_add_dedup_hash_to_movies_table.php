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
        if (! Schema::hasColumn('movies', 'dedup_hash')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->string('dedup_hash', 32)->nullable()->after('imdb_id');
                $table->unique('dedup_hash', 'movies_dedup_hash_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('movies', 'dedup_hash')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->dropUnique('movies_dedup_hash_unique');
                $table->dropColumn('dedup_hash');
            });
        }
    }
};
