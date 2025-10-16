<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportOmdbKeysRequest;
use App\Models\OmdbApiKey;
use Carbon\CarbonInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Date;

class ImportOmdbKeysController extends Controller
{
    public function __invoke(ImportOmdbKeysRequest $request): JsonResponse
    {
        $keys = collect($request->keys());

        if ($keys->isEmpty()) {
            return response()->json([
                'imported' => 0,
                'updated' => 0,
            ]);
        }

        $imported = 0;
        $updated = 0;
        $now = Date::now();

        foreach ($keys as $entry) {
            $attributes = $this->buildAttributes($entry);

            $model = OmdbApiKey::query()->updateOrCreate(
                ['key' => $entry['key']],
                $attributes
            );

            if ($model->wasRecentlyCreated) {
                if (! array_key_exists('first_seen_at', $entry)) {
                    $model->forceFill(['first_seen_at' => $now])->save();
                }

                $imported++;

                continue;
            }

            $updated++;
        }

        return response()->json([
            'imported' => $imported,
            'updated' => $updated,
        ]);
    }

    /**
     * @param  array<string, mixed>  $entry
     * @return array<string, mixed>
     */
    protected function buildAttributes(array $entry): array
    {
        $attributes = [];

        if (array_key_exists('status', $entry)) {
            $attributes['status'] = $entry['status'];
        }

        if (array_key_exists('first_seen_at', $entry)) {
            $attributes['first_seen_at'] = $this->normalizeTimestamp($entry['first_seen_at']);
        }

        if (array_key_exists('last_checked_at', $entry)) {
            $attributes['last_checked_at'] = $this->normalizeTimestamp($entry['last_checked_at']);
        }

        if (array_key_exists('last_confirmed_at', $entry)) {
            $attributes['last_confirmed_at'] = $this->normalizeTimestamp($entry['last_confirmed_at']);
        }

        if (array_key_exists('last_response_code', $entry)) {
            $attributes['last_response_code'] = $entry['last_response_code'];
        }

        return $attributes;
    }

    protected function normalizeTimestamp(mixed $value): ?CarbonInterface
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof CarbonInterface) {
            return $value;
        }

        return Date::parse($value);
    }
}
