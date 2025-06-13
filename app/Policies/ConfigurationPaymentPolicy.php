<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class ConfigurationPaymentPolicy
{
    public function hasAccess(Employee $employee, $permission)
    {
        return $employee->isAdmin() || $employee->hasPermissionTo($permission);
    }

    public function view(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Payment Config List');
    }

    public function save(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Payment Config Create');
    }
    public function editStatus(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Payment Config Status');
    }
    public function destroy(Employee $employee): bool
    {
        return $this->hasAccess($employee, 'Payment Config Delete');
    }
}
