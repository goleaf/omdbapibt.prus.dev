<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Session;
use Laravel\Cashier\Billable;

class User extends Authenticatable
{
    public const IMPERSONATOR_SESSION_KEY = 'impersonator_id';

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable;

    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'preferred_locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function canBeImpersonated(): bool
    {
        return ! $this->isAdmin();
    }

    public static function impersonatorId(): ?int
    {
        $impersonatorId = Session::get(self::IMPERSONATOR_SESSION_KEY);

        return is_numeric($impersonatorId) ? (int) $impersonatorId : null;
    }

    public static function clearImpersonation(): void
    {
        Session::forget(self::IMPERSONATOR_SESSION_KEY);
    }
}
