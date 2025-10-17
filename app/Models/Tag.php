<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
    use HasFactory;

    use ResolvesTranslations;

    public const TYPE_SYSTEM = 'system';

    public const TYPE_COMMUNITY = 'community';

    /**
     * @var list<string>
     */
    public const TYPES = [
        self::TYPE_SYSTEM,
        self::TYPE_COMMUNITY,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'name_i18n',
        'type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name_i18n' => 'array',
    ];

    /**
     * Movies associated with the tag.
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'film_tag')
            ->withPivot(['user_id', 'weight'])
            ->withTimestamps();
    }

    public function localizedName(?string $locale = null): string
    {
        $translations = $this->name_i18n;
        $fallback = is_array($translations) ? Arr::first($translations, fn ($value) => is_string($value) && $value !== '', '') : null;

        if (! $fallback) {
            $fallback = $this->slug ?? '';
        }

        return $this->resolveLocalizedValue(
            is_array($translations) ? $translations : null,
            $fallback,
            $locale,
        );
    }

    public function getNameAttribute(): string
    {
        return $this->localizedName();
    }
}
