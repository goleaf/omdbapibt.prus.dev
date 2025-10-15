<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserManagementLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'target_user_id',
        'action',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }
}
