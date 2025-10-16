<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public const TOTAL_COUNTRIES = 1000;

    public function run(): void
    {
        $countries = Country::factory()
            ->count(self::TOTAL_COUNTRIES)
            ->sequence(function (Sequence $sequence): array {
                $position = $sequence->index + 1;
                $code = $this->twoCharacterCode($position);
                $label = sprintf('Country %04d', $position);

                return [
                    'code' => $code,
                    'name' => $label,
                    'name_translations' => [
                        'en' => $label,
                        'es' => sprintf('País %04d', $position),
                        'fr' => sprintf('Pays %04d', $position),
                    ],
                    'active' => true,
                ];
            })
            ->make()
            ->map(function (Country $country): array {
                return [
                    'code' => $country->code,
                    'name' => $country->getRawOriginal('name') ?? $country->name,
                    'name_translations' => json_encode($country->name_translations, JSON_UNESCAPED_UNICODE),
                    'active' => $country->active,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

        Country::query()->upsert(
            $countries->all(),
            ['code'],
            ['name', 'name_translations', 'active', 'updated_at']
        );

        Country::query()->whereNotIn('code', $countries->pluck('code'))->delete();
    }

    private function twoCharacterCode(int $position): string
    {
        $value = ($position - 1) % 1296; // 36^2 combinations

        return strtoupper(str_pad(base_convert((string) $value, 10, 36), 2, '0', STR_PAD_LEFT));
    }
}
