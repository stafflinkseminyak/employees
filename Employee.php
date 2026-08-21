<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id', 'first_name', 'middle_name', 'last_name', 'title', 'position_title',
        'division_id', 'sub_division_id', 'position_id', 'employment_basis', 'phone', 'home_phone', 'work_phone', 'work_extension',
        'address', 'address_1', 'address_2', 'address_3', 'city', 'territory', 'postcode', 'country',
        'id_number', 'gender', 'blood_type', 'allergies', 'medical_conditions', 'medical_notes',
        'religion', 'birth_info', 'email', 'personal_email', 'visa_type', 'visa_expiry',
        'start_date', 'end_date', 'probation_required', 'probation_end_date',
        'notice_during_probation', 'notice_period',
        'status', 'avatar_path', 'team', 'user_id', 'created_by', 'extra_details',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'probation_required' => 'boolean',
        'probation_end_date' => 'date',
        'visa_expiry'        => 'date',
        'extra_details' => 'array',
    ];

    public function contract() { return $this->belongsTo(Contract::class); }
    public function division() { return $this->belongsTo(Division::class); }
    public function subDivision() { return $this->belongsTo(SubDivision::class); }
    public function position() { return $this->belongsTo(Position::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function payrollDetail() { return $this->hasOne(EmployeePayrollDetail::class); }
    public function employmentDetail() { return $this->hasOne(EmployeeEmploymentDetail::class); }
    public function documents() { return $this->hasMany(EmployeeDocument::class); }

    /** Company assets (laptop, phone, etc.) currently on loan to this employee — managed from the Inventory/Assets page. */
    public function equipmentOnLoan() { return $this->hasMany(InventoryAsset::class, 'employee_id')->orderByDesc('assigned_date'); }

    /**
     * This employee's current Division/Sub-Division/Position — from their
     * Contract when they have one (division_id column + sub_division_id/
     * position_id stored inside contract.form_data, the only place those two
     * live), falling back to this Employee record's own division_id/
     * sub_division_id/position_id columns when there's no Contract (e.g. an
     * intern assigned a Division/Position directly on the Employee page,
     * without ever going through a Contract).
     *
     * Centralizes a fallback chain that used to be duplicated (and drifted
     * out of sync) across KpiTemplate::forEmployee(),
     * AdminKpiJobController::saveKpiTemplate(), and the Saved KPI Templates
     * list — route anything that needs "this person's current position"
     * through here so they all agree.
     */
    public function resolvedPositionIds(): array
    {
        $contract = $this->contract;
        if ($contract) {
            $formData = is_array($contract->form_data) ? $contract->form_data : [];
            return [
                'division_id' => $contract->division_id,
                'sub_division_id' => $formData['sub_division_id'] ?? null,
                'position_id' => $formData['position_id'] ?? null,
            ];
        }

        return [
            'division_id' => $this->division_id,
            'sub_division_id' => $this->sub_division_id,
            'position_id' => $this->position_id,
        ];
    }
    public function passports() { return $this->hasMany(EmployeeDocument::class)->where('type', EmployeeDocument::TYPE_PASSPORT); }
    public function visas() { return $this->hasMany(EmployeeDocument::class)->where('type', EmployeeDocument::TYPE_VISA); }
    public function drivingLicences() { return $this->hasMany(EmployeeDocument::class)->where('type', EmployeeDocument::TYPE_DRIVING_LICENCE); }
    public function emergencyContacts() { return $this->hasMany(EmployeeEmergencyContact::class); }
    public function primaryEmergencyContact() { return $this->hasOne(EmployeeEmergencyContact::class)->where('is_primary', true); }
    public function folders() { return $this->hasMany(EmployeeFolder::class); }
    public function files()   { return $this->hasMany(EmployeeFile::class); }
    public function absences() { return $this->hasMany(EmployeeAbsence::class); }
    public function scopeActive($q) { return $q->where('status', 'active'); }
    public function scopeTerminated($q) { return $q->where('status', 'terminated'); }

    public function getFullNameAttribute(): string {
        return trim(implode(' ', array_filter([$this->first_name, $this->middle_name, $this->last_name], fn ($part) => filled($part))));
    }

    public function getInitialsAttribute(): string {
        return mb_strtoupper(mb_substr(trim($this->first_name), 0, 1)) . mb_strtoupper(mb_substr(trim($this->last_name), 0, 1));
    }

    public function getAvatarColorAttribute(): string {
        $colors = ['#5c6bc0', '#ef5350', '#26a69a', '#ab47bc', '#42a5f5', '#ff7043', '#8d6e63', '#66bb6a', '#ec407a', '#78909c', '#ffa726', '#7e57c2', '#29b6f6', '#d4e157', '#26c6da'];
        return $colors[abs(crc32($this->first_name . $this->last_name)) % count($colors)];
    }

    public static function parseName(string $fullName): array {
        $parts = preg_split('/\s+/', trim($fullName), 2);
        return ['first_name' => $parts[0] ?? '', 'last_name' => $parts[1] ?? ''];
    }
}
