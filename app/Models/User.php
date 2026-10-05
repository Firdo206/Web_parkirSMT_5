<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN   = 'superadmin';
    public const ROLE_ADMIN_WEB    = 'admin_web';
    public const ROLE_ADMIN_PARKIR = 'admin_parkir';
    public const ROLE_MEMBER       = 'member';

    public const ADMIN_ROLES = [
        self::ROLE_ADMIN_WEB    => 'Admin Web (Konten & Langganan)',
        self::ROLE_ADMIN_PARKIR => 'Admin Parkir (Operasional)',
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
            'is_active' => 'boolean',
        ];
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function homeRoute(): string
    {
        return match ($this->role) {
            self::ROLE_SUPERADMIN => route('superadmin.admins.index'),
            default               => route('dashboard'),
        };
    }
}