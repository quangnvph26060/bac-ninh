<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class WalletTransactionPolicy
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
        return $this->hasAccess($employee, 'Deposit History View');
    }

    public function confirm(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Deposit Request Confirm');
    }
    public function refuse(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Deposit Request Refuse');
    }
}
