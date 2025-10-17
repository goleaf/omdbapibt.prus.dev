<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    /** @use HasFactory<\Database\Factories\GenreFactory> */
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
        'slug',
        'tmdb_id',
    ];

    /**
     * Attribute casting configuration.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'name_translations' => 'array',
    ];

    /**
     * Movies that belong to the genre.
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_genre')->withTimestamps();
    }

    public function tvShows(): BelongsToMany
    {
        return $this->belongsToMany(TvShow::class, 'tv_show_genre')->withTimestamps();
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
