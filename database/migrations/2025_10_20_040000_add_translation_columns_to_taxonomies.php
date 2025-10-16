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
            if (Schema::hasColumn('genres', 'name')) {
                $table->dropIndex('genres_name_index');
            }

            if (! Schema::hasColumn('genres', 'name_translations')) {
                $table->json('name_translations')->default(json_encode([]))->after('slug');
            }
        });

        if (Schema::hasColumn('genres', 'name')) {
            DB::table('genres')
                ->select(['id', 'name'])
                ->orderBy('id')
                ->chunkById(100, function ($genres): void {
                    foreach ($genres as $genre) {
                        DB::table('genres')
                            ->where('id', $genre->id)
                            ->update([
                                'name_translations' => json_encode(['en' => $genre->name]),
                            ]);
                    }
                });

            Schema::table('genres', function (Blueprint $table): void {
                $table->dropColumn('name');
            });
        }
    }

    protected function upgradeLanguages(): void
    {
        Schema::table('languages', function (Blueprint $table): void {
            if (Schema::hasColumn('languages', 'name')) {
                $table->dropIndex('languages_name_index');
            }

            if (! Schema::hasColumn('languages', 'name_translations')) {
                $table->json('name_translations')->default(json_encode([]))->after('code');
            }

            if (! Schema::hasColumn('languages', 'native_name_translations')) {
                $table->json('native_name_translations')->default(json_encode([]))->after('name_translations');
            }
        });

        if (Schema::hasColumn('languages', 'name')) {
            DB::table('languages')
                ->select(['id', 'name', 'native_name'])
                ->orderBy('id')
                ->chunkById(100, function ($languages): void {
                    foreach ($languages as $language) {
                        $nativeName = $language->native_name ?? $language->name;

                        DB::table('languages')
                            ->where('id', $language->id)
                            ->update([
                                'name_translations' => json_encode(['en' => $language->name]),
                                'native_name_translations' => json_encode(['en' => $nativeName]),
                            ]);
                    }
                });

            Schema::table('languages', function (Blueprint $table): void {
                $table->dropColumn(['name', 'native_name']);
            });
        }
    }

    protected function upgradeCountries(): void
    {
        Schema::table('countries', function (Blueprint $table): void {
            if (Schema::hasColumn('countries', 'name')) {
                $table->dropIndex('countries_name_index');
            }

            if (! Schema::hasColumn('countries', 'name_translations')) {
                $table->json('name_translations')->default(json_encode([]))->after('code');
            }
        });

        if (Schema::hasColumn('countries', 'name')) {
            DB::table('countries')
                ->select(['id', 'name'])
                ->orderBy('id')
                ->chunkById(100, function ($countries): void {
                    foreach ($countries as $country) {
                        DB::table('countries')
                            ->where('id', $country->id)
                            ->update([
                                'name_translations' => json_encode(['en' => $country->name]),
                            ]);
                    }
                });

            Schema::table('countries', function (Blueprint $table): void {
                $table->dropColumn('name');
            });
        }
    }

    protected function downgradeGenres(): void
    {
        if (! Schema::hasColumn('genres', 'name')) {
            Schema::table('genres', function (Blueprint $table): void {
                $table->string('name')->after('slug');
            });

            DB::table('genres')
                ->select(['id', 'name_translations'])
                ->orderBy('id')
                ->chunkById(100, function ($genres): void {
                    foreach ($genres as $genre) {
                        $translations = json_decode($genre->name_translations ?? '{}', true) ?: [];
                        $value = $translations['en'] ?? reset($translations) ?: null;

                        DB::table('genres')
                            ->where('id', $genre->id)
                            ->update([
                                'name' => $value,
                            ]);
                    }
                });

            Schema::table('genres', function (Blueprint $table): void {
                $table->dropColumn('name_translations');
                $table->index('name');
            });
        }
    }

    protected function downgradeLanguages(): void
    {
        if (! Schema::hasColumn('languages', 'name')) {
            Schema::table('languages', function (Blueprint $table): void {
                $table->string('name')->after('id');
                $table->string('native_name')->nullable()->after('name');
            });

            DB::table('languages')
                ->select(['id', 'name_translations', 'native_name_translations'])
                ->orderBy('id')
                ->chunkById(100, function ($languages): void {
                    foreach ($languages as $language) {
                        $nameTranslations = json_decode($language->name_translations ?? '{}', true) ?: [];
                        $nativeTranslations = json_decode($language->native_name_translations ?? '{}', true) ?: [];

                        $name = $nameTranslations['en'] ?? reset($nameTranslations) ?: null;
                        $native = $nativeTranslations['en'] ?? reset($nativeTranslations) ?: $name;

                        DB::table('languages')
                            ->where('id', $language->id)
                            ->update([
                                'name' => $name,
                                'native_name' => $native,
                            ]);
                    }
                });

            Schema::table('languages', function (Blueprint $table): void {
                $table->dropColumn(['name_translations', 'native_name_translations']);
                $table->index('name');
            });
        }
    }

    protected function downgradeCountries(): void
    {
        if (! Schema::hasColumn('countries', 'name')) {
            Schema::table('countries', function (Blueprint $table): void {
                $table->string('name')->after('code');
            });

            DB::table('countries')
                ->select(['id', 'name_translations'])
                ->orderBy('id')
                ->chunkById(100, function ($countries): void {
                    foreach ($countries as $country) {
                        $translations = json_decode($country->name_translations ?? '{}', true) ?: [];
                        $value = $translations['en'] ?? reset($translations) ?: null;

                        DB::table('countries')
                            ->where('id', $country->id)
                            ->update([
                                'name' => $value,
                            ]);
                    }
                });

            Schema::table('countries', function (Blueprint $table): void {
                $table->dropColumn('name_translations');
                $table->index('name');
            });
        }
    }
};
