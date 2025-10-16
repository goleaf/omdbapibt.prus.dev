<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    /** @use HasFactory<\Database\Factories\UserProfileFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'display_name',
        'tagline',
        'bio',
        'location',
        'timezone',
        'birthday',
        'preferred_language',
        'preferred_audio_language',
        'preferred_subtitle_language',
        'content_maturity',
        'autoplay_next_episode',
        'autoplay_trailers',
        'newsletter_opt_in',
        'marketing_opt_in',
        'favorite_genre',
        'favorite_movie',
        'favorite_tv_show',
        'favorite_actor',
        'favorite_director',
        'favorite_quote',
        'website_url',
        'twitter_url',
        'instagram_url',
        'tiktok_url',
        'youtube_url',
        'letterboxd_url',
        'discord_handle',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birthday' => 'date',
            'autoplay_next_episode' => 'boolean',
            'autoplay_trailers' => 'boolean',
            'newsletter_opt_in' => 'boolean',
            'marketing_opt_in' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
