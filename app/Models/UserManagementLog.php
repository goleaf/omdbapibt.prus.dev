<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserManagementLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'admin_user_id',
        'target_user_id',
        'action',
        'payload',
        'performed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'payload' => 'array',
        'performed_at' => 'datetime',
    ];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_user_id');
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    public static function record(User $admin, ?User $target, string $action, array $payload = []): self
    {
        return static::create([
            'admin_user_id' => $admin->getKey(),
            'target_user_id' => $target?->getKey(),
            'action' => $action,
            'payload' => $payload,
            'performed_at' => now(),
        ]);
    }
}
