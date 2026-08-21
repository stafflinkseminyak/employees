<?php
/**
 * Save this file to: app/Models/ManagerExternal.php
 *
 * Represents a person at the top of a reporting chain (e.g. a Director/
 * owner) who is deliberately NOT an Employee record — no contract, payroll,
 * documents, or personal data, just enough to show their name/title as the
 * final link in someone's "Reports to" chain. Kept as its own tiny table
 * instead of a stripped-down Employee row so there is never any HR/personal
 * data associated with them, by construction.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagerExternal extends Model
{
    protected $table = 'manager_externals';

    protected $fillable = ['name', 'title'];

    public function directReports()
    {
        return $this->hasMany(Employee::class, 'manager_external_id');
    }
}
