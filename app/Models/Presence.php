<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presence extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'office_location_id',
        'latitude',
        'longitude',
        'check_out_latitude',  
        'check_out_longitude',
        'work_type',
        'check_in',
        'check_out',
        'date',
        'status',
        'is_late',
        'photo_path',
    ];

    protected $casts = [
        'is_late' => 'boolean',
    ];

    // Define the relationship with the Employee model
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function officeLocation()
    {
        return $this->belongsTo(OfficeLocation::class);
    }
}
