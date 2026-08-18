<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number','template','division_id','employee_name','employee_title',
        'employee_id_number','employee_user_id','position_title','employment_basis','company_name',
        'employer_name','first_party_name','agreement_date','start_date','end_date',
        'salary_total','salary_base','salary_allowance','food_allowance','transport_allowance',
        'accommodation_allowance','bpjs_jkk_risk_level',
        'bpjs_ketenagakerjaan_employer','bpjs_ketenagakerjaan_employee',
        'bpjs_kesehatan_employer','bpjs_kesehatan_employee',
        'tax_amount','total_salary_package','take_home_pay',
        'form_data','status','created_by','approved_by','rejected_by','approved_at',
        'rejected_at','rejection_reason',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'salary_total' => 'decimal:2',
        'salary_base' => 'decimal:2',
        'salary_allowance' => 'decimal:2',
        'form_data' => 'array',
        'food_allowance' => 'decimal:2',
        'transport_allowance' => 'decimal:2',
        'accommodation_allowance' => 'decimal:2',
        'bpjs_ketenagakerjaan_employer' => 'decimal:2',
        'bpjs_ketenagakerjaan_employee' => 'decimal:2',
        'bpjs_kesehatan_employer' => 'decimal:2',
        'bpjs_kesehatan_employee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_salary_package' => 'decimal:2',
        'take_home_pay' => 'decimal:2',
    ];

    public function templateLabel(): string
    {
        return match ($this->template) {
            'fixed_term_pkwt' => 'PKWT',
            'permanent_pkwtt' => 'PKWTT',
            'internship' => 'Internship',
            default => strtoupper((string) $this->template),
        };
    }

    // Relationships
    public function division() { return $this->belongsTo(\App\Models\Division::class); }
    public function creator() { return $this->belongsTo(\App\Models\User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(\App\Models\User::class, 'approved_by'); }
    public function rejector() { return $this->belongsTo(\App\Models\User::class, 'rejected_by'); }
    public function employee() { return $this->hasOne(\App\Models\Employee::class); }

    // Scopes
    public function scopePending($q) { return $q->where('status','pending_approval'); }
    public function scopeApproved($q) { return $q->where('status','approved'); }
    public function scopeRejected($q) { return $q->where('status','rejected'); }

    // Helpers
    public function isPending(): bool { return $this->status === 'pending_approval'; }
    public function isApproved(): bool { return $this->status === 'approved'; }
    public function isRejected(): bool { return $this->status === 'rejected'; }

    public function getCreatedAtPerthAttribute(): string
    {
        return $this->created_at
            ? $this->created_at->copy()->setTimezone('Asia/Singapore')->format('d M Y, H:i:s') . ' (Perth Time UTC+8)'
            : '';
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_approval' => 'Pending Approval',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
            default => ucfirst($this->status ?? 'unknown'),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending_approval' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}