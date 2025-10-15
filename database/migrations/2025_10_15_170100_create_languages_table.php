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
        Schema::table('languages', function (Blueprint $table): void {
            $table->string('native_name')->nullable(false)->change();
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('languages', function (Blueprint $table): void {
            $table->dropIndex(['name']);
            $table->string('native_name')->nullable()->change();
        });
    }
};
