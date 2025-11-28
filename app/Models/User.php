<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
        'hospital_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Define user roles as constants
     */
    const ROLE_SUPERADMIN = 'superadmin';
    const ROLE_ADMIN = 'admin';
    const ROLE_MUTU = 'mutu';
    const ROLE_KLAIM = 'klaim';
    const ROLE_MANAJEMEN = 'manajemen';
    const ROLE_OBSERVER = 'observer';

    /**
     * Check if user has a specific role
     *
     * @param string $role
     * @return bool
     */
    public function hasRole($role)
    {
        // Superadmin has access to all roles
        if ($this->role === self::ROLE_SUPERADMIN) {
            return true;
        }
        
        return $this->role === $role;
    }
    
    /**
     * Check if user is a superadmin
     *
     * @return bool
     */
    public function isSuperadmin()
    {
        return $this->role === self::ROLE_SUPERADMIN;
    }

    /**
     * Check if user is an observer (read-only access)
     *
     * @return bool
     */
    public function isObserver()
    {
        return $this->role === self::ROLE_OBSERVER;
    }

    /**
     * Get the hospital that owns this user.
     */
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Get audit logs related to this user.
     */
    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'user_id');
    }
}
