<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'omdb_id',
        'slug',
        'title',
        'original_title',
        'year',
        'runtime',
        'release_date',
        'plot',
        'tagline',
        'homepage',
        'budget',
        'revenue',
        'status',
        'popularity',
        'vote_average',
        'vote_count',
        'poster_path',
        'backdrop_path',
        'trailer_url',
        'media_type',
        'adult',
        'video',
        'translations',
        'cast',
        'crew',
        'streaming_links',
        'trailers',
    ];

    protected $casts = [
        'release_date' => 'date',
        'adult' => 'boolean',
        'video' => 'boolean',
        'translations' => 'array',
        'cast' => 'array',
        'crew' => 'array',
        'streaming_links' => 'array',
        'trailers' => 'array',
    ];

    /**
     * Attempt to resolve a movie from either a slug or primary key when route binding.
     */
    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $query = static::query();

        if ($field) {
            return $query->where($field, $value)->first();
        }

        return $query
            ->where('slug', $value)
            ->orWhereKey($value)
            ->first();
    }

    /**
     * Get the default route key name.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Retrieve translated content for the requested field and locale.
     */
    public function translated(string $field, ?string $locale = null, ?string $fallbackLocale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $fallbackLocale = $fallbackLocale ?? config('app.fallback_locale');

        $translations = Arr::get($this->translations ?? [], $field, []);

        if (is_string($translations)) {
            return $translations;
        }

        if (! is_array($translations)) {
            return $this->getAttribute($field);
        }

        return $translations[$locale]
            ?? ($fallbackLocale && ($translations[$fallbackLocale] ?? null))
            ?? $this->getAttribute($field);
    }

    /**
     * Convenience accessor for the movie rating in percentage form.
     */
    protected function ratingPercent(): Attribute
    {
        return Attribute::get(function () {
            if (! $this->vote_average) {
                return null;
            }

            return (int) round($this->vote_average * 10);
        });
    }
}
