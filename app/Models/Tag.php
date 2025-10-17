<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<\Database\Factories\TagFactory> */
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
        'description',
        'description_translations',
        'metadata',
        'is_active',
        'is_featured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'name_translations' => 'array',
            'description_translations' => 'array',
            'metadata' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_tag')->withTimestamps();
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(ListModel::class, 'list_tag')->withTimestamps();
    }

    public function localizedName(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->name_translations, $this->getRawOriginal('name'), $locale);
    }

    public function localizedDescription(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->description_translations, $this->getRawOriginal('description'), $locale);
    }
}
