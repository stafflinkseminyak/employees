<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'document_no',
        'country',
        'class',
        'expiry_date',
        'notes',
    ];

    protected $casts = [
        'expiry_date' => 'date',
    ];

    // Document type constants — use these everywhere instead of raw strings
    const TYPE_PASSPORT         = 'passport';
    const TYPE_VISA             = 'visa';
    const TYPE_DRIVING_LICENCE  = 'driving_licence';

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Check if this document is expired or expiring soon.
     * Usage: $doc->isExpiringSoon(30) — true if expires within 30 days
     */
    public function isExpiringSoon(int $days = 30): bool
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->lte(now()->addDays($days));
    }

    public function isExpired(): bool
    {
        if (!$this->expiry_date) return false;
        return $this->expiry_date->lt(now());
    }
}