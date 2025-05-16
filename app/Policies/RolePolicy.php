<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class RolePolicy
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
        return $this->hasAccess($employee, 'Role View');
    }
    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Role Create');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Role Edit');
    }

    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Role Destroy');
    }
}
