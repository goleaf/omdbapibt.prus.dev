<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_translations',
        'code',
        'active',
    ];

    /**
     * Attribute casts.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name_translations' => 'array',
        'active' => 'boolean',
    ];

    /**
     * Movies that originate from the country.
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_country')->withTimestamps();
    }

    /**
     * Retrieve the localized country name.
     */
    public function localizedName(?string $locale = null): ?string
    {
        return $this->translationFor('name_translations', $locale);
    }

    /**
     * Provide backwards compatible access to the "name" attribute.
     */
    protected function name(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->localizedName());
    }

    protected function translationFor(string $attribute, ?string $locale = null): ?string
    {
        $translations = $this->getAttribute($attribute);

        if (! is_array($translations) || $translations === []) {
            return null;
        }

        $locale ??= app()->getLocale();

        $value = $this->valueForLocale($translations, $locale);

        if (! is_null($value)) {
            return $value;
        }

        $fallbackLocale = config('app.fallback_locale');
        $value = $this->valueForLocale($translations, $fallbackLocale);

        if (! is_null($value)) {
            return $value;
        }

        $value = $this->valueForLocale($translations, 'en');

        if (! is_null($value)) {
            return $value;
        }

        foreach ($translations as $translation) {
            if (is_string($translation) && $translation !== '') {
                return $translation;
            }
        }

        return null;
    }

    protected function valueForLocale(array $translations, ?string $locale): ?string
    {
        if (! $locale) {
            return null;
        }

        $value = $translations[$locale] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }
}
