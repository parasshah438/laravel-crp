<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'log_type',
        'user_type',
        'user_id',
        'user_email',
        'activity',
        'description',
        'properties',
        'method',
        'url',
        'user_agent',
        'ip_address',
        'country',
        'region',
        'city',
        'latitude',
        'longitude',
        'timezone',
        'browser',
        'platform',
        'device',
        'status'
    ];

    protected $casts = [
        'properties' => 'array',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeByUser(Builder $query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByUserType(Builder $query, $userType)
    {
        return $query->where('user_type', $userType);
    }

    public function scopeByLogType(Builder $query, $logType)
    {
        return $query->where('log_type', $logType);
    }

    public function scopeByDateRange(Builder $query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ]);
    }

    public function scopeByIpAddress(Builder $query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    public function scopeSearch(Builder $query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('activity', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('user_email', 'like', "%{$search}%")
              ->orWhere('ip_address', 'like', "%{$search}%")
              ->orWhere('url', 'like', "%{$search}%");
        });
    }

    public function getLocationAttribute()
    {
        $location = collect([$this->city, $this->region, $this->country])
            ->filter()
            ->implode(', ');
        
        return $location ?: 'Unknown';
    }

    public function getFormattedPropertiesAttribute()
    {
        if (!$this->properties) {
            return null;
        }

        return json_encode($this->properties, JSON_PRETTY_PRINT);
    }

    public function getBrowserInfoAttribute()
    {
        $info = collect([$this->browser, $this->platform, $this->device])
            ->filter()
            ->implode(' / ');
        
        return $info ?: 'Unknown';
    }
}