<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Country extends Model
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
        'active',
    ];

    /**
     * Attribute casting configuration.
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

    public function localizedName(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->name_translations, $this->getRawOriginal('name'), $locale);
    }

    public function getNameAttribute(?string $value): string
    {
        return $this->localizedName();
    }
}
