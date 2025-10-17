<?php

namespace Database\Seeders\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait SeedsTranslatableTable
{
    /**
     * Seed the given table with translatable baseline records.
     *
     * @param  array<int, array<string, mixed>>  $records
     */
    protected function seedTranslatableTable(string $table, array $records): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        $columns = Schema::getColumnListing($table);
        $identifier = $this->determineIdentifierColumn($columns);

        if ($identifier === null) {
            return;
        }

        $model = $this->makeDynamicModel($table, $columns);

        foreach ($records as $index => $record) {
            $attributes = [
                $identifier => $identifier === 'slug'
                    ? $record['slug']
                    : ($record['code'] ?? $this->codeFromSlug($record['slug'])),
            ];

            $payload = $this->buildCommonPayload($record, $columns, $identifier, $index);

            $model->newQuery()->updateOrCreate($attributes, $payload);
        }
    }

    /**
     * Determine which column should be used as the unique identifier when seeding.
     *
     * @param  array<int, string>  $columns
     */
    private function determineIdentifierColumn(array $columns): ?string
    {
        if (in_array('slug', $columns, true)) {
            return 'slug';
        }

        if (in_array('code', $columns, true)) {
            return 'code';
        }

        return null;
    }

    /**
     * Create a lightweight model instance for the target table so we can call updateOrCreate.
     *
     * @param  array<int, string>  $columns
     */
    private function makeDynamicModel(string $table, array $columns): Model
    {
        return new class($table, $columns) extends Model
        {
            /**
             * @param  array<int, string>  $columns
             */
            public function __construct(private readonly string $tableName, private readonly array $columns)
            {
                parent::__construct();

                $this->setTable($this->tableName);
                $this->guarded = [];

                $casts = [];

                foreach (['name_translations', 'description_translations', 'short_name_translations'] as $column) {
                    if (in_array($column, $this->columns, true)) {
                        $casts[$column] = 'array';
                    }
                }

                if ($casts !== []) {
                    $this->casts = $casts;
                }
            }
        };
    }

    /**
     * Build the payload that will be persisted via updateOrCreate.
     *
     * @param  array<string, mixed>  $record
     * @param  array<int, string>  $columns
     */
    private function buildCommonPayload(array $record, array $columns, string $identifier, int $index): array
    {
        $payload = [];

        if ($identifier !== 'slug' && in_array('slug', $columns, true)) {
            $payload['slug'] = $record['slug'];
        }

        if ($identifier !== 'code' && in_array('code', $columns, true)) {
            $payload['code'] = $record['code'] ?? $this->codeFromSlug($record['slug']);
        }

        $nameTranslations = $record['name_translations'] ?? [];
        if ($nameTranslations !== []) {
            if (in_array('name_translations', $columns, true)) {
                $payload['name_translations'] = $nameTranslations;
            }

            if (in_array('name', $columns, true)) {
                $payload['name'] = $this->resolvePrimaryText($nameTranslations, $record['slug']);
            }
        }

        $shortNameTranslations = $record['short_name_translations'] ?? [];
        if ($shortNameTranslations !== []) {
            if (in_array('short_name_translations', $columns, true)) {
                $payload['short_name_translations'] = $shortNameTranslations;
            }

            if (in_array('short_name', $columns, true)) {
                $payload['short_name'] = $this->resolvePrimaryText($shortNameTranslations, $record['slug']);
            }
        }

        $descriptionTranslations = $record['description_translations'] ?? [];
        if ($descriptionTranslations !== []) {
            if (in_array('description_translations', $columns, true)) {
                $payload['description_translations'] = $descriptionTranslations;
            }

            if (in_array('description', $columns, true)) {
                $payload['description'] = $this->resolvePrimaryText($descriptionTranslations, null);
            }
        }

        if (in_array('sort_order', $columns, true)) {
            $payload['sort_order'] = $record['sort_order'] ?? ($index + 1);
        } elseif (in_array('position', $columns, true)) {
            $payload['position'] = $record['sort_order'] ?? ($index + 1);
        }

        $active = $record['active'] ?? $record['is_active'] ?? true;

        if (in_array('active', $columns, true)) {
            $payload['active'] = $active;
        }

        if (in_array('is_active', $columns, true)) {
            $payload['is_active'] = $active;
        }

        foreach ($record as $key => $value) {
            if (in_array($key, [
                'slug',
                'code',
                'name_translations',
                'short_name_translations',
                'description_translations',
                'active',
                'is_active',
                'sort_order',
            ], true)) {
                continue;
            }

            if (in_array($key, $columns, true)) {
                $payload[$key] = $value;
            }
        }

        return $payload;
    }

    private function codeFromSlug(string $slug): string
    {
        return Str::upper(Str::replace('-', '_', $slug));
    }

    /**
     * Resolve the primary text value for the provided translations.
     *
     * @param  array<string, string>  $translations
     */
    private function resolvePrimaryText(array $translations, ?string $fallback): string
    {
        if (isset($translations['en']) && is_string($translations['en']) && $translations['en'] !== '') {
            return $translations['en'];
        }

        foreach ($translations as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return $fallback ?? '';
    }
}
