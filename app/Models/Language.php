<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    use HasFactory;
    use ResolvesTranslations;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'name_translations',
        'code',
        'native_name',
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

    public function tvShows(): BelongsToMany
    {
        return $this->belongsToMany(TvShow::class, 'tv_show_language')->withTimestamps();
    }

    public function localizedName(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->name_translations, $this->getRawOriginal('name'), $locale);
    }

    public function localizedNativeName(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->native_name_translations, $this->getRawOriginal('native_name'), $locale);
    }

    public function getNameAttribute(?string $value): string
    {
        return $this->localizedName();
    }

    public function getNativeNameAttribute(?string $value): ?string
    {
        $localized = $this->localizedNativeName();

        return $localized === '' ? $value : $localized;
    }
}
