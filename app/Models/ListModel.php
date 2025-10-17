<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListModel extends Model
{
    use HasFactory;

    public const WATCH_LATER_TITLE = 'Watch Later';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lists';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'public',
        'description',
        'cover_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'public' => 'boolean',
    ];

    /**
     * The owning user for the list.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Items contained within the list.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ListItem::class, 'list_id');
    }

    /**
     * Movies that belong to the list.
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'list_items', 'list_id', 'movie_id')
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Scope the query to the default "watch later" list.
     */
    public function scopeWatchLater(Builder $query): Builder
    {
        return $query->where('title', self::WATCH_LATER_TITLE);
    }

    /**
     * Determine if the list represents the default "watch later" collection.
     */
    public function isWatchLater(): bool
    {
        return $this->title === self::WATCH_LATER_TITLE;
    }

    /**
     * Retrieve the next position for a new item.
     */
    public function nextPosition(): int
    {
        $current = $this->items()->max('position');

        return $current ? ((int) $current + 1) : 1;
    }
}
