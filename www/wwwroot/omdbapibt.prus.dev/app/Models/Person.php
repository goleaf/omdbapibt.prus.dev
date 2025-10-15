<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Person extends Model
{
    use HasFactory;
    use HasTranslations;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tmdb_id',
        'imdb_id',
        'slug',
        'name',
        'also_known_as',
        'biography',
        'birthday',
        'deathday',
        'place_of_birth',
        'gender',
        'known_for_department',
        'popularity',
        'homepage',
        'profile_path',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'also_known_as' => 'array',
        'biography' => 'array',
        'birthday' => 'date',
        'deathday' => 'date',
        'popularity' => 'float',
    ];

    /**
     * Attributes that support translations.
     *
     * @var array<int, string>
     */
    public array $translatable = [
        'biography',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $person): void {
            if (blank($person->slug) && filled($person->name)) {
                $person->slug = static::generateUniqueSlug($person->name);
            }
        });

        static::updating(function (self $person): void {
            if (blank($person->slug) && filled($person->name)) {
                $person->slug = static::generateUniqueSlug($person->name, $person->getKey());
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $query = static::query();

        if ($field) {
            return $query->where($field, $value)->firstOrFail();
        }

        return $query
            ->where('slug', $value)
            ->when(is_numeric($value), fn ($q) => $q->orWhere('id', (int) $value))
            ->firstOr(function () use ($value) {
                throw (new ModelNotFoundException())->setModel(static::class, [$value]);
            });
    }

    protected static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);

        if ($baseSlug === '') {
            $baseSlug = Str::uuid()->toString();
        }

        $slug = $baseSlug;
        $counter = 1;

        while (static::withTrashed()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * The movies the person is credited on.
     */
    public function movieCredits(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'movie_person')
            ->withPivot(['role', 'department', 'character', 'job', 'order'])
            ->withTimestamps();
    }

    /**
     * The TV shows the person is credited on.
     */
    public function tvCredits(): BelongsToMany
    {
        return $this->belongsToMany(TvShow::class, 'tv_show_person')
            ->withPivot(['role', 'department', 'character', 'job', 'order'])
            ->withTimestamps();
    }

    /**
     * Retrieve a human readable label for the person's gender.
     */
    public function genderLabel(): ?string
    {
        if (is_null($this->gender)) {
            return null;
        }

        if (! is_numeric($this->gender)) {
            return (string) $this->gender;
        }

        return match ((int) $this->gender) {
            1 => __('Female'),
            2 => __('Male'),
            3 => __('Non-binary'),
            default => __('Not specified'),
        };
    }

    /**
     * Retrieve the formatted alternate names.
     */
    public function alternateNames(): Collection
    {
        return collect($this->also_known_as ?? [])
            ->filter()
            ->values();
    }

    /**
     * Determine the formatted birthday for display.
     */
    public function formattedBirthday(): ?string
    {
        return $this->formatDate($this->birthday);
    }

    /**
     * Determine the formatted deathday for display.
     */
    public function formattedDeathday(): ?string
    {
        return $this->formatDate($this->deathday);
    }

    /**
     * Format a date instance for output.
     */
    protected function formatDate(?CarbonInterface $date): ?string
    {
        return $date?->translatedFormat('F j, Y');
    }

    /**
     * Resolve an accessible URL for the person's profile image.
     */
    public function profileImageUrl(?string $size = null): string
    {
        $path = $this->profile_path;

        if (blank($path)) {
            return asset('images/placeholders/person.svg');
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, ['storage/', 'images/', 'media/'])) {
            return asset($path);
        }

        if (Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->url($path);
        }

        $baseUrl = rtrim(config('services.tmdb.image_base_url', 'https://image.tmdb.org/t/p'), '/');
        $size = trim($size ?? config('services.tmdb.profile_size', 'w780'), '/');

        return sprintf('%s/%s/%s', $baseUrl, $size, ltrim($path, '/'));
    }
}
