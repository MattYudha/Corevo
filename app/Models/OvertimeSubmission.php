<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OvertimeSubmission extends Model
{
    protected $fillable = [
        'employee_id', 'date', 'start_time', 'end_time', 
        'duration_minutes', 'description', 'evidence_path', 'status', 'approved_by'
    ];

    public function employee() {
        return $this->belongsTo(Employee::class);
    }
}