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
        return $this->hasAccess($employee, 'Employee View');
    }

    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Employee Create');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Employee Edit');
    }

    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Employee Destroy');
    }
}
