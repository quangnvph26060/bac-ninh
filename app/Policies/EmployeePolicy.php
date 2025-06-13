<?php

namespace App\Policies;

use App\Models\Employee;

class EmployeePolicy
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
        return $this->hasAccess($employee, 'View Employee List');
    }

    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Add New Employee');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Staff Update');
    }

    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Delete Employee');
    }
}
