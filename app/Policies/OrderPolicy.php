<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class OrderPolicy
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
        return $this->hasAccess($employee, 'View Order List');
    }

    public function create(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Employee Create');
    }

    public function edit(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'View Order Details');
    }

    public function showItem(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Quick View Of Items');
    }

    public function cancel(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Order Cancel');
    }

    public function print(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Print Invoice');
    }
    public function changeStatus(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Update Order Status');
    }
}
