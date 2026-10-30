<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'status',
        'permission_group_id',
        'sort_order',
        'is_system',
        'dependencies',
        'metadata'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_system' => 'boolean',
        'dependencies' => 'array',
        'metadata' => 'array'
    ];

    protected $hidden = ['created_at', 'updated_at'];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    public function group()
    {
        return $this->belongsTo(PermissionGroup::class, 'permission_group_id');
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

    public function scopeByGroup($query, $groupSlug)
    {
        return $query->whereHas('group', function ($q) use ($groupSlug) {
            $q->where('slug', $groupSlug);
        });
    }

    // Methods
    public function isDeletable()
    {
        return !$this->is_system && $this->roles()->count() === 0;
    }

    public function checkDependencies($userPermissions = [])
    {
        if (empty($this->dependencies)) {
            return true;
        }

        foreach ($this->dependencies as $dependency) {
            if (!in_array($dependency, $userPermissions)) {
                return false;
            }
        }

        return true;
    }

    // Cache helper
    public static function getCached()
    {
        return Cache::remember('permissions.all', 3600, function () {
            return self::with('group')->active()->orderBy('sort_order')->get();
        });
    }

    public static function clearCache()
    {
        Cache::forget('permissions.all');
        Cache::forget('roles.permissions');
    }

    protected static function booted()
    {
        static::saved(function () {
            self::clearCache();
        });

        static::deleted(function () {
            self::clearCache();
        });
    }
}