<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class KPI extends Model
{
    protected $table = 'kpis';

    protected $fillable = [
        'code',
        'name',
        'category',
        'description',
        'formula',
        'target_value',
        'min_value',
        'max_value',
        'weight',
        'unit',
        'status',
        'metric_category',
        'metric_key',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    /**
     * Get all employee KPI records for this KPI
     */
    public function employeeRecords(): HasMany
    {
        return $this->hasMany(EmployeeKPIRecord::class);
    }

    /**
     * Get the roles assigned to this KPI
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_kpi', 'kpi_id', 'role_id')
                    ->withPivot('target_value', 'weight');
    }

    /**
     * Scope to filter active KPIs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get KPI categories
     */
    public static function getCategories()
    {
        return [
            'Attendance' => 'Attendance & Presence Metrics',
            'Productivity' => 'Task Completion & Productivity Metrics',
            'Leave' => 'Leave & Time-Off Management',
            'Salary' => 'Salary & Compensation Metrics',
            'Department' => 'Department & Role-Based Performance',
            'Behavior' => 'Behavior & Conduct Metrics',
            'Quality' => 'Quality & Efficiency Metrics',
        ];
    }

    /**
     * Calculate performance level based on actual value and target
     */
    public static function calculatePerformanceLevel($actualValue, $targetValue)
    {
        if ($actualValue >= $targetValue * 0.9) {
            return 'excellent';
        } elseif ($actualValue >= $targetValue * 0.75) {
            return 'good';
        } elseif ($actualValue >= $targetValue * 0.6) {
            return 'satisfactory';
        } elseif ($actualValue >= $targetValue * 0.45) {
            return 'needs_improvement';
        }
        return 'unsatisfactory';
    }

    /**
     * Calculate status
     */
    public static function calculateStatus($actualValue, $targetValue)
    {
        if ($actualValue >= $targetValue) {
            return 'achieved';
        } elseif ($actualValue >= $targetValue * 0.8) {
            return 'warning';
        }
        return 'critical';
    }
}
