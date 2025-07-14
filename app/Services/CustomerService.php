<?php

namespace App\Services;

use App\Models\Customer;


class CustomerService extends BaseService
{
    public function __construct(Customer $customer)
    {
        parent::__construct($customer);
    }

    public function pagination()
    {
        return $this->queryBuilder();
    }

    public function getUserById($id)
    {
        return $this->findById($id, ['*'], ['orders', 'orders.orderItems', 'wallet'], [], [], ['orders']);
    }
}
