<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\RolePermissionException;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'level',
        'is_system',
        'metadata'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_system' => 'boolean',
        'metadata' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    // Constants for role hierarchy
    const LEVEL_SUPER_ADMIN = 100;
    const LEVEL_ADMIN = 50;
    const LEVEL_MODERATOR = 25;
    const LEVEL_USER = 1;

    // Relationships
    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions')
                   ->withTimestamps();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', '>=', $level);
    }

    // Permission methods with caching
    public function hasPermission($permission)
    {
        $cacheKey = "role.{$this->id}.permissions";
        $permissions = Cache::remember($cacheKey, 1800, function () {
            return $this->permissions->pluck('slug')->toArray();
        });

        if (is_string($permission)) {
            return in_array($permission, $permissions);
        }

        if (is_array($permission)) {
            return !empty(array_intersect($permission, $permissions));
        }

        return in_array($permission->slug ?? null, $permissions);
    }

    public function hasAllPermissions(array $permissions)
    {
        $rolePermissions = $this->getCachedPermissions();
        return empty(array_diff($permissions, $rolePermissions));
    }

    public function hasAnyPermission(array $permissions)
    {
        $rolePermissions = $this->getCachedPermissions();
        return !empty(array_intersect($permissions, $rolePermissions));
    }

    public function givePermissionTo($permission, $validateHierarchy = true)
    {
        if ($validateHierarchy && !$this->canModifyPermissions()) {
            throw new RolePermissionException('Insufficient privileges to modify permissions.');
        }

        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->active()->first();
        }

        if (!$permission) {
            throw new RolePermissionException('Permission not found or inactive.');
        }

        if (!$this->hasPermission($permission)) {
            $this->permissions()->attach($permission->id);
            $this->clearPermissionCache();
        }

        return $this;
    }

    public function revokePermissionTo($permission, $validateHierarchy = true)
    {
        if ($validateHierarchy && !$this->canModifyPermissions()) {
            throw new RolePermissionException('Insufficient privileges to modify permissions.');
        }

        if (is_string($permission)) {
            $permission = Permission::where('slug', $permission)->first();
        }

        if ($permission && $this->hasPermission($permission)) {
            $this->permissions()->detach($permission->id);
            $this->clearPermissionCache();
        }

        return $this;
    }

    public function syncPermissions(array $permissions, $validateHierarchy = true)
    {
        if ($validateHierarchy && !$this->canModifyPermissions()) {
            throw new RolePermissionException('Insufficient privileges to modify permissions.');
        }

        $permissionIds = Permission::whereIn('slug', $permissions)
                                   ->active()
                                   ->pluck('id')
                                   ->toArray();

        $this->permissions()->sync($permissionIds);
        $this->clearPermissionCache();

        return $this;
    }

    // Hierarchy methods
    public function canModifyPermissions()
    {
        $currentUser = Auth::guard('admin')->user() ?? Auth::user();
        
        if (!$currentUser || !$currentUser->role) {
            return false;
        }

        // Super admin can modify all
        if ($currentUser->role->level >= self::LEVEL_SUPER_ADMIN) {
            return true;
        }

        // Users can only modify roles with lower level than their own
        return $currentUser->role->level > $this->level;
    }

    public function isDeletable()
    {
        return !$this->is_system && 
               $this->users()->count() === 0 && 
               $this->admins()->count() === 0;
    }

    // Cache methods
    public function getCachedPermissions()
    {
        $cacheKey = "role.{$this->id}.permissions";
        return Cache::remember($cacheKey, 1800, function () {
            return $this->permissions->pluck('slug')->toArray();
        });
    }

    public function clearPermissionCache()
    {
        Cache::forget("role.{$this->id}.permissions");
        Cache::forget('roles.permissions');
    }

    protected static function booted()
    {
        static::saved(function ($role) {
            $role->clearPermissionCache();
        });

        static::deleted(function ($role) {
            $role->clearPermissionCache();
        });
    }
}