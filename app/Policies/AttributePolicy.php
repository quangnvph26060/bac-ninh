<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class AttributePolicy
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
        return $this->hasAccess($employee, 'View Attribute List');
    }

    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Add New Attribute');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Update Attribute');
    }

    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Delete Attribute');
    }
}
