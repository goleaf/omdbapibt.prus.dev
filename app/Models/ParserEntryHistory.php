<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParserEntryHistory extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'parser_entry_id',
        'user_id',
        'action',
        'changes',
        'notes',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'changes' => 'array',
    ];

    public function entry(): BelongsTo
    {
        return $this->belongsTo(ParserEntry::class, 'parser_entry_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
