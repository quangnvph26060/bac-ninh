<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class ProductPolicy
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
        return $this->hasAccess($employee, 'Product View');
    }

    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Product Create');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Product Edit');
    }

    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Product Destroy');
    }

    public function import(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Product Import');
    }

    public function export(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Product Export');
    }

    public function downloadTemplate(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Product Download Template');
    }
}
