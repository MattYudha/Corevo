<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'assigned_to', 'due_date', 'status', 
        'completed_at', 'quality_rating', 'quality_notes', 'reviewed_by', 'reviewed_at',
        'priority', 'estimated_hours', 'actual_hours', 'created_by',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'completed_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the employee that is assigned to the task.
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    /**
     * Get the employee who reviewed this task.
     */
    public function reviewer()
    {
        return $this->belongsTo(Employee::class, 'reviewed_by');
    }

    /**
     * Get all comments for this task.
     */
    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    /**
     * Accessor helper for employee name so views can safely show a name.
     */
    public function getEmployeeNameAttribute()
    {
        return $this->employee?->fullname ?? 'Unknown Employee';
    }
}
