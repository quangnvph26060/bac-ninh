<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class SupplierPolicy
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
        return $this->hasAccess($employee, 'View List Of Suppliers');
    }

    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Add New Supplier');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Supplier Update');
    }

    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Delete Supplier');
    }
}
