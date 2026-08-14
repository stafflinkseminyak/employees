<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'parent_id',
        'name',
        'color',
        'doc_type',
        'is_required',
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(EmployeeFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(EmployeeFolder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(EmployeeFile::class, 'folder_id');
    }
}
