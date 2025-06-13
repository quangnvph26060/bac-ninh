<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class ConfigurationPolicy
{
    public function hasAccess(Employee $employee, $permission)
    {
        return $employee->isAdmin() || $employee->hasPermissionTo($permission);
    }

    /**
     * Determine whether the user can view any models.
     */
    public function view(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Manage Conflig');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Update General Configuration');
    }
}
