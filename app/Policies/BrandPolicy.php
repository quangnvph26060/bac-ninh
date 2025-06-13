<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class BrandPolicy
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
        return $this->hasAccess($employee, 'View Brand List');
    }

    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Add New Brand');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Brand Update');
    }

    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Remove Brand');
    }
}
