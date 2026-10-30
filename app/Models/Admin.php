<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->role && $this->role->slug === $role;
        }
        
        if (is_array($role)) {
            return $this->role && in_array($this->role->slug, $role);
        }

        return false;
    }

    public function hasPermission($permission)
    {
        return $this->role && $this->role->hasPermission($permission);
    }

    public function hasAllPermissions($permissions)
    {
        return $this->role && $this->role->hasAllPermissions($permissions);
    }

    public function hasAnyPermission($permissions)
    {
        return $this->role && $this->role->hasAnyPermission($permissions);
    }

    public function can($ability, $arguments = [])
    {
        // Support Laravel's Gate system
        if (method_exists($this, 'hasPermission')) {
            return $this->hasPermission($ability);
        }
        
        return parent::can($ability, $arguments);
    }

    public function getAllPermissions()
    {
        return $this->role ? $this->role->getCachedPermissions() : [];
    }

    public function isSuperAdmin()
    {
        return $this->hasRole('superadmin');
        
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }
}