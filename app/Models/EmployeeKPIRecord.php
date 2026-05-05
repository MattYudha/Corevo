<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeKPIRecord extends Model
{
    protected $table = 'employee_kpi_records';

    protected $fillable = [
        'employee_id',
        'kpi_id',
        'period',
        'actual_value',
        'target_value',
        'previous_value',
        'status',
        'notes',
        'calculation_method',
        'composite_score',
        'performance_level',
        'submission_status',
        'submitted_at',
        'reviewed_by',
        'reviewed_at',
        'reviewer_notes',
    ];

    protected $casts = [
        'actual_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'previous_value' => 'decimal:2',
        'composite_score' => 'decimal:2',
    ];

    /**
     * Get the employee for this KPI record
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the KPI definition
     */
    public function kpi(): BelongsTo
    {
        return $this->belongsTo(KPI::class);
    }

    /**
     * Scope to filter by period
     */
    public function scopeByPeriod($query, $period)
    {
        return $query->where('period', $period);
    }

    /**
     * Scope to filter by employee
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope to filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get percentage achievement
     */
    public function getAchievementPercentage()
    {
        if ($this->target_value == 0) {
            return 0;
        }
        return ($this->actual_value / $this->target_value) * 100;
    }

    /**
     * Get variance from target
     */
    public function getVariance()
    {
        return $this->actual_value - $this->target_value;
    }

    /**
     * Get percentage change from previous period
     */
    public function getPercentageChange()
    {
        if (is_null($this->previous_value) || $this->previous_value == 0) {
            return 0;
        }
        return (($this->actual_value - $this->previous_value) / $this->previous_value) * 100;
    }
}
