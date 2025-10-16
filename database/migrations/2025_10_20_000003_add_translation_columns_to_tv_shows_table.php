<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            if (! Schema::hasColumn('tv_shows', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
            if (! Schema::hasColumn('tv_shows', 'overview_translations')) {
                $table->json('overview_translations')->nullable()->after('overview');
            }
            if (! Schema::hasColumn('tv_shows', 'tagline_translations')) {
                $table->json('tagline_translations')->nullable()->after('tagline');
            }
        });

        // Only migrate data if columns were just added
        if (! Schema::hasColumn('tv_shows', 'name_translations')) {
            DB::table('tv_shows')
                ->select(['id', 'name', 'overview', 'tagline'])
                ->orderBy('id')
                ->chunkById(100, function ($shows): void {
                    foreach ($shows as $show) {
                        DB::table('tv_shows')
                            ->where('id', $show->id)
                            ->update([
                                'name_translations' => self::encodeTranslation($show->name),
                                'overview_translations' => self::encodeTranslation($show->overview),
                                'tagline_translations' => self::encodeTranslation($show->tagline),
                            ]);
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::table('tv_shows', function (Blueprint $table): void {
            $table->dropColumn(['name_translations', 'overview_translations', 'tagline_translations']);
        });
    }

    private static function encodeTranslation(?string $value): ?string
    {
        if ($value === null || trim($value) === '') {
            return null;
        }

        return json_encode(['en' => $value], JSON_THROW_ON_ERROR);
    }
};
