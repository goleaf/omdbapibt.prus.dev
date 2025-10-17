<?php

namespace Database\Seeders;

use App\Models\Platform;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class PlatformSeeder extends Seeder
{
    /**
     * Seed core streaming platforms supported by the catalog.
     */
    public function run(): void
    {
        if (! Schema::hasTable('platforms')) {
            return;
        }

        $records = [
            [
                'slug' => 'netflix',
                'name' => 'Netflix',
                'url' => 'https://www.netflix.com',
                'regions' => ['global', 'us', 'ca', 'gb'],
            ],
            [
                'slug' => 'prime-video',
                'name' => 'Prime Video',
                'url' => 'https://www.primevideo.com',
                'regions' => ['global', 'us', 'in', 'de'],
            ],
            [
                'slug' => 'max',
                'name' => 'Max',
                'url' => 'https://www.max.com',
                'regions' => ['us', 'latam', 'nordics'],
            ],
            [
                'slug' => 'disney-plus',
                'name' => 'Disney+',
                'url' => 'https://www.disneyplus.com',
                'regions' => ['global', 'emea', 'apac'],
            ],
        ];

        $hasNameTranslations = Schema::hasColumn('platforms', 'name_translations');
        $hasWebsiteUrl = Schema::hasColumn('platforms', 'website_url');
        $hasUrl = Schema::hasColumn('platforms', 'url');
        $hasMetadata = Schema::hasColumn('platforms', 'metadata');
        $hasType = Schema::hasColumn('platforms', 'type');
        $hasIsFeatured = Schema::hasColumn('platforms', 'is_featured');
        $hasIsActive = Schema::hasColumn('platforms', 'is_active');

        foreach ($records as $record) {
            $platform = Platform::query()->firstOrNew(['slug' => $record['slug']]);

            $platform->name = $record['name'];

            if ($hasNameTranslations) {
                $platform->name_translations = [
                    'en' => $record['name'],
                ];
            }

            if ($hasWebsiteUrl) {
                $platform->website_url = $record['url'];
            } elseif ($hasUrl) {
                $platform->url = $record['url'];
            }

            if ($hasMetadata) {
                $platform->metadata = [
                    'region_hints' => $record['regions'],
                ];
            }

            if ($hasType) {
                $platform->type = 'streaming';
            }

            if ($hasIsFeatured) {
                $platform->is_featured = true;
            }

            if ($hasIsActive) {
                $platform->is_active = true;
            }

            $platform->save();
        }

        Platform::query()->whereNotIn('slug', array_column($records, 'slug'))->delete();
    }
}
