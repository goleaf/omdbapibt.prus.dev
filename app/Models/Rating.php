<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\RatingFactory> */
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ratings';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key associated with the table.
     *
     * @var string|null
     */
    protected $primaryKey = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'liked',
        'disliked',
        'rated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'liked' => 'boolean',
        'disliked' => 'boolean',
        'rated_at' => 'datetime',
    ];

    /**
     * Scope ratings that represent positive feedback.
     */
    public function scopeLiked(Builder $query): Builder
    {
        return $query->where('liked', true);
    }

    /**
     * Scope ratings that represent negative feedback.
     */
    public function scopeDisliked(Builder $query): Builder
    {
        return $query->where('disliked', true);
    }

    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
