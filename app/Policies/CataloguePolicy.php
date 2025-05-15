<?php

namespace App\Policies;

use App\Models\Category;
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
    public function viewAny(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Category ViewAny');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Category Create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Category Update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Category Delete');
    }
}
