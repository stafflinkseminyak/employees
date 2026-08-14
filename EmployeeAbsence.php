<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAbsence extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'start_date',
        'end_date',
        'ongoing',
        'company_paid',
        'evidenced',
        'reason',
        'late_hours',
        'late_minutes',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'ongoing'      => 'boolean',
        'company_paid' => 'boolean',
        'evidenced'    => 'boolean',
    ];

    const TYPE_ANNUAL   = 'annual';
    const TYPE_PERSONAL = 'personal';
    const TYPE_LATENESS = 'lateness';
    const TYPE_OTHER    = 'other';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
