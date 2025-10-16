<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
                    $nameTranslations = $this->buildTranslationPayload($show->name);
                    $overviewTranslations = $this->buildTranslationPayload($show->overview);
                    $taglineTranslations = $this->buildTranslationPayload($show->tagline);

                    DB::table('tv_shows')
                        ->where('id', $show->id)
                        ->update([
                            'name_translations' => $nameTranslations !== null ? json_encode($nameTranslations, JSON_UNESCAPED_UNICODE) : null,
                            'overview_translations' => $overviewTranslations !== null ? json_encode($overviewTranslations, JSON_UNESCAPED_UNICODE) : null,
                            'tagline_translations' => $taglineTranslations !== null ? json_encode($taglineTranslations, JSON_UNESCAPED_UNICODE) : null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        if (! Schema::hasTable('tv_shows')) {
            return;
        }

        Schema::table('tv_shows', function (Blueprint $table): void {
            $columns = ['name_translations', 'overview_translations', 'tagline_translations'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('tv_shows', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function buildTranslationPayload(?string $value): ?array
    {
        $trimmed = is_string($value) ? trim($value) : '';

        if ($trimmed === '') {
            return null;
        }

        return ['en' => $trimmed];
    }
};
