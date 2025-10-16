<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('tv_shows')) {
            return;
        }

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

        DB::table('tv_shows')
            ->select(['id', 'name', 'overview', 'tagline'])
            ->orderBy('id')
            ->chunkById(100, function ($shows): void {
                foreach ($shows as $show) {
                    $translations = [];

                    if (! empty($show->name)) {
                        $translations['name_translations'] = json_encode(['en' => $show->name], JSON_THROW_ON_ERROR);
                    }

                    if (! empty($show->overview)) {
                        $translations['overview_translations'] = json_encode(['en' => $show->overview], JSON_THROW_ON_ERROR);
                    }

                    if (! empty($show->tagline)) {
                        $translations['tagline_translations'] = json_encode(['en' => $show->tagline], JSON_THROW_ON_ERROR);
                    }

                    if (! empty($translations)) {
                        DB::table('tv_shows')
                            ->where('id', $show->id)
                            ->update($translations);
                    }
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('tv_shows')) {
            return;
        }

        DB::table('tv_shows')
            ->select(['id', 'name_translations', 'overview_translations', 'tagline_translations'])
            ->orderBy('id')
            ->chunkById(100, function ($shows): void {
                foreach ($shows as $show) {
                    $updates = [];

                    $nameTranslations = is_string($show->name_translations)
                        ? json_decode($show->name_translations, true, 512, JSON_THROW_ON_ERROR)
                        : $show->name_translations;

                    if (is_array($nameTranslations) && isset($nameTranslations['en'])) {
                        $updates['name'] = $nameTranslations['en'];
                    }

                    $overviewTranslations = is_string($show->overview_translations)
                        ? json_decode($show->overview_translations, true, 512, JSON_THROW_ON_ERROR)
                        : $show->overview_translations;

                    if (is_array($overviewTranslations) && isset($overviewTranslations['en'])) {
                        $updates['overview'] = $overviewTranslations['en'];
                    }

                    $taglineTranslations = is_string($show->tagline_translations)
                        ? json_decode($show->tagline_translations, true, 512, JSON_THROW_ON_ERROR)
                        : $show->tagline_translations;

                    if (is_array($taglineTranslations) && isset($taglineTranslations['en'])) {
                        $updates['tagline'] = $taglineTranslations['en'];
                    }

                    if (! empty($updates)) {
                        DB::table('tv_shows')
                            ->where('id', $show->id)
                            ->update($updates);
                    }
                }
            });

        Schema::table('tv_shows', function (Blueprint $table): void {
            if (Schema::hasColumn('tv_shows', 'name_translations')) {
                $table->dropColumn('name_translations');
            }

            if (Schema::hasColumn('tv_shows', 'overview_translations')) {
                $table->dropColumn('overview_translations');
            }

            if (Schema::hasColumn('tv_shows', 'tagline_translations')) {
                $table->dropColumn('tagline_translations');
            }
        });
    }
};
