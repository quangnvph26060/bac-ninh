<?php

namespace App\Policies;

use App\Models\Employee;

class CataloguePolicy
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
        return $this->hasAccess($employee, 'View List Of Categories');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Add New Category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Update Catalog');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Delete Category');
    }
}
