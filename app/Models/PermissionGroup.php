<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'sort_order'
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class)->orderBy('sort_order');
    }
}