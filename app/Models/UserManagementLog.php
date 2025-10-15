<?php

namespace App\Models;

use App\Enums\UserManagementAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserManagementLog extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'actor_id',
        'user_id',
        'action',
        'details',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'action' => UserManagementAction::class,
        'details' => 'array',
    ];

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
