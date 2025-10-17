<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Platform extends Model
{
    /** @use HasFactory<\Database\Factories\PlatformFactory> */
    use HasFactory;
    use ResolvesTranslations;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'name_translations',
        'slug',
        'type',
        'website_url',
        'metadata',
        'is_active',
        'is_featured',
        'launch_country_id',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name_translations' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_platform')->withTimestamps();
    }

    public function localizedName(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->name_translations, $this->getRawOriginal('name'), $locale);
    }
}
