<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeePayrollDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        // Gaji
        'salary',
        'overtime_ph_allowance',
        'transport_allowance',
        'meals_allowance',
        'position_allowance',
        'accommodation_allowance',
        'pay_rate',
        'pay_frequency',
        'payroll_no',
        // Data sensitif (dienkripsi otomatis)
        'tfn',
        'bank_acc_name',
        'bank_acc_no',
        'bank_bsb',
        // Bank
        'bank_name',
        'bank_branch',
        // Superannuation (Australia)
        'super_fund_name',
        'super_fund_abn',
        'super_member_no',
        'super_usi',
        // BPJS Ketenagakerjaan (Indonesia)
        'bpjs_ketenagakerjaan_no',
        'bpjs_ketenagakerjaan_start',
        'bpjs_ketenagakerjaan_active',
        // BPJS Kesehatan (Indonesia)
        'bpjs_kesehatan_no',
        'bpjs_kesehatan_class',
        'bpjs_kesehatan_dependants',
        'bpjs_kesehatan_start',
        'bpjs_kesehatan_active',
        'bpjs_kesehatan_percent',
    ];

    protected $casts = [
        'salary'   => 'decimal:2',
        'overtime_ph_allowance' => 'decimal:2',
        'transport_allowance'   => 'decimal:2',
        'meals_allowance'       => 'decimal:2',
        'position_allowance'    => 'decimal:2',
        'accommodation_allowance' => 'decimal:2',
        'bpjs_kesehatan_percent' => 'decimal:2',
        'pay_rate' => 'string', // stores text label e.g. "Hour", "Per month" — not a number
        // Field sensitif dienkripsi di database
        'tfn'           => 'encrypted',
        'bank_acc_name' => 'encrypted',
        'bank_acc_no'   => 'encrypted',
        'bank_bsb'      => 'encrypted',
        // Tanggal
        'bpjs_ketenagakerjaan_start' => 'date',
        'bpjs_kesehatan_start'       => 'date',
        // Boolean
        'bpjs_ketenagakerjaan_active' => 'boolean',
        'bpjs_kesehatan_active'       => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getBpjsDeductionAttribute(): float
    {
        return round(((float) $this->salary) * ((float) ($this->bpjs_kesehatan_percent ?? 5)) / 100, 2);
    }

    public function getMealsAfterBpjsAttribute(): float
    {
        return round(((float) $this->meals_allowance) - $this->bpjs_deduction, 2);
    }

    public function getNetSalaryAttribute(): float
    {
        return round(
            ((float) $this->salary)
            + ((float) $this->overtime_ph_allowance)
            + ((float) $this->transport_allowance)
            + $this->meals_after_bpjs
            + ((float) $this->position_allowance)
            + ((float) $this->accommodation_allowance),
            2
        );
    }
}
