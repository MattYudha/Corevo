<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OfficeLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'location_type',
        'address',
        'latitude',
        'longitude',
        'radius',
        'allowed_ssids',
        'allowed_ips', 
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'radius' => 'integer',
            'allowed_ssids' => 'array',
            'allowed_ips' => 'array',
            'deleted_at' => 'datetime',
        ];
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->location_type) {
            'head_office' => 'Pusat',
            'branch' => 'Cabang',
            default => 'Lainnya',
        };
    }
}
