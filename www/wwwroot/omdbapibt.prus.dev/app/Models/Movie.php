<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    /** @use HasFactory<\Database\Factories\MovieFactory> */
    use HasFactory;
    use SoftDeletes;

    /**
     * Mass assignment protection is disabled to keep imports flexible.
     *
     * @var list<string>
     */
    protected $guarded = [];

    /**
     * Watch history entries linked to the movie.
     */
    public function watchHistories(): HasMany
    {
        return $this->hasMany(WatchHistory::class);
    }
}
