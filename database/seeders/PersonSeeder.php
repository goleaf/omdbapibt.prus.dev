<?php

namespace Database\Seeders;

use App\Models\Person;
use Database\Seeders\Concerns\SeedsModelsInChunks;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    use SeedsModelsInChunks;

    private const TOTAL_PEOPLE = 1000;

    private const CHUNK_SIZE = 250;

    /**
     * Seed a catalog of people that can be attached to media credits.
     */
    public function run(): void
    {
        if (Person::query()->exists()) {
            return;
        }

        $fallbackLocale = $this->fallbackLocale();

        $this->seedInChunks(self::TOTAL_PEOPLE, self::CHUNK_SIZE, function (int $count) use ($fallbackLocale): void {
            Person::factory()
                ->count($count)
                ->create()
                ->each(function (Person $person) use ($fallbackLocale): void {
                    $biography = $person->biography ?? '';

                    $translations = $this->fillTranslations(
                        $person->biography_translations,
                        $biography !== '' ? $biography : null,
                        fn (string $locale, ?string $fallback) => $locale === $fallbackLocale && $fallback !== null
                            ? $fallback
                            : $this->localizedParagraph($locale)
                    );

                    $person->forceFill([
                        'biography_translations' => $translations,
                    ])->saveQuietly();
                });
        });
    }
}
