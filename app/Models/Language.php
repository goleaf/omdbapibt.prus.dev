<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
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
        'native_name_translations',
        'active',
    ];

    /**
     * Attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name_translations' => 'array',
        'native_name_translations' => 'array',
        'active' => 'boolean',
    ];

    /**
     * Movies that include the language.
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_language')->withTimestamps();
    }

    /**
     * Retrieve the localized language name.
     */
    public function localizedName(?string $locale = null): ?string
    {
        return $this->translationFor('name_translations', $locale);
    }

    /**
     * Retrieve the localized native name.
     */
    public function localizedNativeName(?string $locale = null): ?string
    {
        return $this->translationFor('native_name_translations', $locale);
    }

    /**
     * Provide backwards compatible access to the "name" attribute.
     */
    protected function name(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->localizedName());
    }

    /**
     * Provide backwards compatible access to the "native_name" attribute.
     */
    protected function nativeName(): Attribute
    {
        return Attribute::get(fn (): ?string => $this->localizedNativeName());
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
