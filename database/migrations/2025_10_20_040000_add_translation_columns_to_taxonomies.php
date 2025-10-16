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
        $this->upgradeGenres();
        $this->upgradeLanguages();
        $this->upgradeCountries();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->downgradeGenres();
        $this->downgradeLanguages();
        $this->downgradeCountries();
    }

    protected function upgradeGenres(): void
    {
        Schema::table('genres', function (Blueprint $table): void {
            if (! Schema::hasColumn('genres', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
        });

        DB::table('genres')
            ->select(['id', 'name', 'name_translations'])
            ->orderBy('id')
            ->chunkById(100, function ($genres): void {
                foreach ($genres as $genre) {
                    $translations = json_decode($genre->name_translations ?? '[]', true) ?: [];

                    if (! array_key_exists('en', $translations) && $genre->name) {
                        $translations['en'] = $genre->name;
                    }

                    if ($translations === []) {
                        continue;
                    }

                    DB::table('genres')
                        ->where('id', $genre->id)
                        ->update([
                            'name_translations' => json_encode($translations, JSON_UNESCAPED_UNICODE),
                        ]);
                }
            });
    }

    protected function upgradeLanguages(): void
    {
        Schema::table('languages', function (Blueprint $table): void {
            if (! Schema::hasColumn('languages', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }

            if (! Schema::hasColumn('languages', 'native_name_translations')) {
                $table->json('native_name_translations')->nullable()->after('native_name');
            }
        });

        DB::table('languages')
            ->select(['id', 'name', 'native_name', 'name_translations', 'native_name_translations'])
            ->orderBy('id')
            ->chunkById(100, function ($languages): void {
                foreach ($languages as $language) {
                    $nameTranslations = json_decode($language->name_translations ?? '[]', true) ?: [];
                    $nativeTranslations = json_decode($language->native_name_translations ?? '[]', true) ?: [];

                    if (! array_key_exists('en', $nameTranslations) && $language->name) {
                        $nameTranslations['en'] = $language->name;
                    }

                    $nativeFallback = $language->native_name ?? $language->name;
                    if (! array_key_exists('en', $nativeTranslations) && $nativeFallback) {
                        $nativeTranslations['en'] = $nativeFallback;
                    }

                    $payload = [];

                    if ($nameTranslations !== []) {
                        $payload['name_translations'] = json_encode($nameTranslations, JSON_UNESCAPED_UNICODE);
                    }

                    if ($nativeTranslations !== []) {
                        $payload['native_name_translations'] = json_encode($nativeTranslations, JSON_UNESCAPED_UNICODE);
                    }

                    if ($payload !== []) {
                        DB::table('languages')
                            ->where('id', $language->id)
                            ->update($payload);
                    }
                }
            });
    }

    protected function upgradeCountries(): void
    {
        Schema::table('countries', function (Blueprint $table): void {
            if (! Schema::hasColumn('countries', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
        });

        DB::table('countries')
            ->select(['id', 'name', 'name_translations'])
            ->orderBy('id')
            ->chunkById(100, function ($countries): void {
                foreach ($countries as $country) {
                    $translations = json_decode($country->name_translations ?? '[]', true) ?: [];

                    if (! array_key_exists('en', $translations) && $country->name) {
                        $translations['en'] = $country->name;
                    }

                    if ($translations === []) {
                        continue;
                    }

                    DB::table('countries')
                        ->where('id', $country->id)
                        ->update([
                            'name_translations' => json_encode($translations, JSON_UNESCAPED_UNICODE),
                        ]);
                }
            });
    }

    protected function downgradeGenres(): void
    {
        if (Schema::hasColumn('genres', 'name_translations')) {
            Schema::table('genres', function (Blueprint $table): void {
                $table->dropColumn('name_translations');
            });
        }
    }

    protected function downgradeLanguages(): void
    {
        Schema::table('languages', function (Blueprint $table): void {
            $columns = [];

            if (Schema::hasColumn('languages', 'name_translations')) {
                $columns[] = 'name_translations';
            }

            if (Schema::hasColumn('languages', 'native_name_translations')) {
                $columns[] = 'native_name_translations';
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }

    protected function downgradeCountries(): void
    {
        if (Schema::hasColumn('countries', 'name_translations')) {
            Schema::table('countries', function (Blueprint $table): void {
                $table->dropColumn('name_translations');
            });
        }
    }
};
