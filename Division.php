<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function subDivisions()
    {
        return $this->hasMany(SubDivision::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
