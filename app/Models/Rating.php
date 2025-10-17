<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

class Rating extends Model
{
    /** @use HasFactory<\Database\Factories\RatingFactory> */
    use HasFactory;

    /**
     * @var list<string>
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
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'liked' => 'boolean',
            'disliked' => 'boolean',
            'rated_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Rating $rating): void {
            if ($rating->liked && $rating->disliked) {
                throw ValidationException::withMessages([
                    'disliked' => 'A rating cannot be liked and disliked at the same time.',
                ]);
            }

            if ($rating->rating === null) {
                return;
            }

            if ($rating->rating < 1 || $rating->rating > 10) {
                throw ValidationException::withMessages([
                    'rating' => 'The rating must be between 1 and 10.',
                ]);
            }
        });
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
