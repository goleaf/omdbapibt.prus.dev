<?php

namespace App\Models;

use App\Support\TmdbImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Person extends Model
{
    use HasFactory;
    use HasTranslations;

    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'slug',
        'name',
        'biography',
        'birthday',
        'deathday',
        'place_of_birth',
        'gender',
        'known_for_department',
        'popularity',
        'profile_path',
    ];

    protected $casts = [
        'birthday' => 'date',
        'deathday' => 'date',
    ];

    /**
     * @var array<int, string>
     */
    public array $translatable = [
        'biography',
    ];

    protected $appends = [
        'profile_image_url',
    ];

    /**
     * Movies associated with the person.
     */
    public function movieCredits(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_person')
            ->withPivot([
                'role',
                'character',
                'job',
                'department',
                'credit_order',
            ])
            ->withTimestamps()
            ->orderByPivot('credit_order');
    }

    /**
     * TV shows associated with the person.
     */
    public function tvCredits(): BelongsToMany
    {
        return $this->belongsToMany(TvShow::class, 'person_tv_show')
            ->withPivot([
                'role',
                'character',
                'job',
                'department',
                'credit_order',
            ])
            ->withTimestamps()
            ->orderByPivot('credit_order');
    }

    /**
     * The biography translations formatted for display.
     *
     * @return array<int, array{locale: string, label: string, biography: string}>
     */
    public function formattedBiographyTranslations(string $locale): array
    {
        return Collection::make($this->getTranslations('biography'))
            ->filter(fn (?string $biography): bool => filled($biography))
            ->map(fn (string $biography, string $translationLocale): array => [
                'locale' => $translationLocale,
                'label' => strtoupper($translationLocale),
                'biography' => $biography,
                'is_active' => $translationLocale === $locale,
            ])
            ->sortByDesc('is_active')
            ->values()
            ->map(fn (array $payload): array => Collection::make($payload)->except('is_active')->all())
            ->all();
    }

    /**
     * Profile image url accessor.
     */
    public function getProfileImageUrlAttribute(): string
    {
        return TmdbImage::profile($this->profile_path);
    }

    /**
     * Normalized role label helper.
     */
    public function resolveRoleLabel(?string $role): string
    {
        if (blank($role)) {
            return 'Other';
        }

        return Str::headline((string) $role);
    }
}
