<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeEmploymentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'place_of_work',
        'work_country',
        'jurisdiction',
        'employee_type',
        'working_pattern',
        'working_schedule',
        'leave_unit',
        'contracted_hours',
        'contracted_minutes',
        'contracted_days',
        'average_hours',
        'average_minutes',
        'annual_leave_hours',
        'annual_leave_minutes',
        'accrual_rate',
        'effective_from',
        'salary_reason',
    ];

    protected $casts = [
        'effective_from'    => 'date',
        'working_schedule'  => 'array', // stored as JSON, auto cast to/from PHP array
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
