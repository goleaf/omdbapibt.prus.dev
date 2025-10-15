<?php

namespace App\Models;

use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    /** @use HasFactory<\Database\Factories\ReviewFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'movie_title',
        'rating',
        'body',
    ];

    /**
     * The relationships that should always be loaded.
     *
     * @var array<int, string>
     */
    protected $with = [
        'user',
    ];

    /**
     * Accessor for the sanitized body value that is safe to render as HTML.
     */
    public function getSanitizedBodyAttribute(): string
    {
        return HtmlSanitizer::clean($this->body ?? '');
    }

    /**
     * Review author relationship.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
