<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'list_id',
        'movie_id',
        'position',
    ];

    /**
     * The list that owns the item.
     */
    public function list(): BelongsTo
    {
        return $this->belongsTo(ListModel::class, 'list_id');
    }

    /**
     * The movie referenced by the list item.
     */
    public function movie(): BelongsTo
    {
        return $this->belongsTo(Movie::class);
    }
}
