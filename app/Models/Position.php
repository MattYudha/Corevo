<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $primaryKey = 'position_id';

    protected $fillable = ['position_name', 'title', 'level', 'salary_grade', 'description'];

    public function employeePositions()
    {
        return $this->hasMany(Employee::class, 'position_id', 'position_id');
    }
}
