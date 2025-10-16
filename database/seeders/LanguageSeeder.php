<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public const TOTAL_LANGUAGES = 1000;

    public function run(): void
    {
        $languages = Language::factory()
            ->count(self::TOTAL_LANGUAGES)
            ->sequence(function (Sequence $sequence): array {
                $position = $sequence->index + 1;
                $code = 'l'.str_pad((string) $position, 4, '0', STR_PAD_LEFT);
                $label = sprintf('Language %04d', $position);

                return [
                    'code' => $code,
                    'name' => $label,
                    'name_translations' => [
                        'en' => $label,
                        'es' => sprintf('Idioma %04d', $position),
                        'fr' => sprintf('Langue %04d', $position),
                    ],
                    'native_name' => sprintf('Lingua %04d', $position),
                    'native_name_translations' => [
                        'en' => sprintf('Lingua %04d', $position),
                        'es' => sprintf('Lengua %04d', $position),
                        'fr' => sprintf('Langue maternelle %04d', $position),
                    ],
                    'active' => true,
                ];
            })
            ->make()
            ->map(function (Language $language): array {
                return [
                    'code' => $language->code,
                    'name' => $language->getRawOriginal('name') ?? $language->name,
                    'name_translations' => json_encode($language->name_translations, JSON_UNESCAPED_UNICODE),
                    'native_name' => $language->getRawOriginal('native_name') ?? $language->native_name,
                    'native_name_translations' => json_encode($language->native_name_translations, JSON_UNESCAPED_UNICODE),
                    'active' => $language->active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        Language::query()->upsert(
            $languages->all(),
            ['code'],
            ['name', 'name_translations', 'native_name', 'native_name_translations', 'active', 'updated_at']
        );

        Language::query()->whereNotIn('code', $languages->pluck('code'))->delete();
    }
}
