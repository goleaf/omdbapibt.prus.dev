<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('omdb_api_keys', function (Blueprint $table): void {
            if (! Schema::hasColumn('omdb_api_keys', 'consecutive_failures')) {
                $table->unsignedSmallInteger('consecutive_failures')->default(0)->after('last_response_code');
            }

            if (! Schema::hasColumn('omdb_api_keys', 'disabled_until')) {
                $table->timestampTz('disabled_until')->nullable()->after('consecutive_failures');
            }
        });
    }

    public function down(): void
    {
        Schema::table('omdb_api_keys', function (Blueprint $table): void {
            if (Schema::hasColumn('omdb_api_keys', 'disabled_until')) {
                $table->dropColumn('disabled_until');
            }

            if (Schema::hasColumn('omdb_api_keys', 'consecutive_failures')) {
                $table->dropColumn('consecutive_failures');
            }
        });
    }
};
