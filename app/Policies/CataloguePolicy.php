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
        return $this->hasAccess($employee, 'Catalogue View');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Catalogue Create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Catalogue Edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Catalogue Destroy');
    }
}
