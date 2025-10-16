<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('genres', function (Blueprint $table): void {
            if (! Schema::hasColumn('genres', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
        });

        Schema::table('languages', function (Blueprint $table): void {
            if (! Schema::hasColumn('languages', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }

            if (! Schema::hasColumn('languages', 'native_name_translations')) {
                $table->json('native_name_translations')->nullable()->after('native_name');
            }
        });

        Schema::table('countries', function (Blueprint $table): void {
            if (! Schema::hasColumn('countries', 'name_translations')) {
                $table->json('name_translations')->nullable()->after('name');
            }
        });

        DB::table('genres')->orderBy('id')->chunkById(100, function ($genres): void {
            foreach ($genres as $genre) {
                if (! empty($genre->name)) {
                    DB::table('genres')
                        ->where('id', $genre->id)
                        ->update([
                            'name_translations' => json_encode(['en' => $genre->name], JSON_UNESCAPED_UNICODE),
                        ]);
                }
            }
        });

        DB::table('languages')->orderBy('id')->chunkById(100, function ($languages): void {
            foreach ($languages as $language) {
                $nameTranslations = [];
                $nativeNameTranslations = [];

                if (! empty($language->name)) {
                    $nameTranslations['en'] = $language->name;
                }

                if (! empty($language->native_name)) {
                    $nativeNameTranslations['en'] = $language->native_name;
                }

                if ($nameTranslations !== []) {
                    DB::table('languages')
                        ->where('id', $language->id)
                        ->update([
                            'name_translations' => json_encode($nameTranslations, JSON_UNESCAPED_UNICODE),
                            'native_name_translations' => $nativeNameTranslations === []
                                ? null
                                : json_encode($nativeNameTranslations, JSON_UNESCAPED_UNICODE),
                        ]);
                }
            }
        });

        DB::table('countries')->orderBy('id')->chunkById(100, function ($countries): void {
            foreach ($countries as $country) {
                if (! empty($country->name)) {
                    DB::table('countries')
                        ->where('id', $country->id)
                        ->update([
                            'name_translations' => json_encode(['en' => $country->name], JSON_UNESCAPED_UNICODE),
                        ]);
                }
            }
        });
    }

    public function down(): void
    {
        DB::table('genres')->whereNotNull('name_translations')->orderBy('id')->chunkById(100, function ($genres): void {
            foreach ($genres as $genre) {
                $translations = json_decode($genre->name_translations ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                $english = $translations['en'] ?? null;

                if (! is_null($english)) {
                    DB::table('genres')->where('id', $genre->id)->update(['name' => $english]);
                }
            }
        });

        DB::table('languages')->whereNotNull('name_translations')->orderBy('id')->chunkById(100, function ($languages): void {
            foreach ($languages as $language) {
                $nameTranslations = json_decode($language->name_translations ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                $nativeTranslations = json_decode($language->native_name_translations ?? '{}', true, 512, JSON_THROW_ON_ERROR);

                if (isset($nameTranslations['en'])) {
                    DB::table('languages')->where('id', $language->id)->update(['name' => $nameTranslations['en']]);
                }

                if ($nativeTranslations !== []) {
                    DB::table('languages')->where('id', $language->id)->update([
                        'native_name' => $nativeTranslations['en'] ?? reset($nativeTranslations),
                    ]);
                }
            }
        });

        DB::table('countries')->whereNotNull('name_translations')->orderBy('id')->chunkById(100, function ($countries): void {
            foreach ($countries as $country) {
                $translations = json_decode($country->name_translations ?? '{}', true, 512, JSON_THROW_ON_ERROR);
                $english = $translations['en'] ?? null;

                if (! is_null($english)) {
                    DB::table('countries')->where('id', $country->id)->update(['name' => $english]);
                }
            }
        });

        Schema::table('genres', function (Blueprint $table): void {
            if (Schema::hasColumn('genres', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
        });

        Schema::table('languages', function (Blueprint $table): void {
            if (Schema::hasColumn('languages', 'name_translations')) {
                $table->dropColumn('name_translations');
            }

            if (Schema::hasColumn('languages', 'native_name_translations')) {
                $table->dropColumn('native_name_translations');
            }
        });

        Schema::table('countries', function (Blueprint $table): void {
            if (Schema::hasColumn('countries', 'name_translations')) {
                $table->dropColumn('name_translations');
            }
        });
    }
};
