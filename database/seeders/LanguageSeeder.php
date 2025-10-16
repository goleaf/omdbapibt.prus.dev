<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class LanguageSeeder extends Seeder
{
    public const TOTAL_LANGUAGES = 1000;

    public function run(): void
    {
        $hasName = Schema::hasColumn('languages', 'name');
        $hasNativeName = Schema::hasColumn('languages', 'native_name');

        $languages = Language::factory()
            ->count(self::TOTAL_LANGUAGES)
            ->sequence(function (Sequence $sequence): array {
                $position = $sequence->index + 1;
                $code = 'l'.str_pad((string) $position, 4, '0', STR_PAD_LEFT);
                $label = sprintf('Language %04d', $position);

                return [
                    'code' => $code,
                    'name_translations' => [
                        'en' => $label,
                        'es' => sprintf('Idioma %04d', $position),
                        'fr' => sprintf('Langue %04d', $position),
                    ],
                    'native_name_translations' => [
                        'en' => sprintf('Lingua %04d', $position),
                        'es' => sprintf('Lengua %04d', $position),
                        'fr' => sprintf('Langue maternelle %04d', $position),
                    ],
                    'active' => true,
                ];
            })
            ->make()
            ->map(function (Language $language) use ($hasName, $hasNativeName): array {
                $base = [
                    'code' => $language->code,
                    'name_translations' => json_encode($language->name_translations, JSON_UNESCAPED_UNICODE),
                    'native_name_translations' => json_encode($language->native_name_translations, JSON_UNESCAPED_UNICODE),
                    'active' => $language->active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if ($hasName) {
                    $translations = $language->name_translations ?? [];
                    $base['name'] = $translations['en'] ?? (reset($translations) ?: $language->code);
                }

                if ($hasNativeName) {
                    $nativeTranslations = $language->native_name_translations ?? [];
                    $base['native_name'] = $nativeTranslations['en'] ?? (reset($nativeTranslations) ?: ($base['name'] ?? $language->code));
                }

                return $base;
            });

        // SQLite has a low max_variable_number; batch to avoid exceeding it
        collect($languages->all())
            ->chunk(200)
            ->each(function ($chunk): void {
                Language::query()->upsert(
                    $chunk->all(),
                    ['code'],
                    ['name_translations', 'native_name_translations', 'active', 'updated_at']
                );
            });

        Language::query()->whereNotIn('code', $languages->pluck('code'))->delete();
    }
}
