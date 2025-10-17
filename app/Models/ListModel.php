<?php

namespace App\Models;

use App\Models\Concerns\ResolvesTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ListModel extends Model
{
    /** @use HasFactory<\Database\Factories\ListModelFactory> */
    use HasFactory;
    use ResolvesTranslations;

    protected $table = 'lists';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'title_translations',
        'slug',
        'description',
        'description_translations',
        'is_public',
        'visibility',
        'metadata',
        'featured_at',
        'cover_image_url',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'title_translations' => 'array',
            'description_translations' => 'array',
            'is_public' => 'boolean',
            'metadata' => 'array',
            'featured_at' => 'datetime',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'list_movie')->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'list_tag')->withTimestamps();
    }

    public function localizedTitle(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->title_translations, $this->getRawOriginal('title'), $locale);
    }

    public function localizedDescription(?string $locale = null): string
    {
        return $this->resolveLocalizedValue($this->description_translations, $this->getRawOriginal('description'), $locale);
    }
}
