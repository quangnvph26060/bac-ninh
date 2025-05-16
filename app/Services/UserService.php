<?php

namespace App\Services;

use App\Models\User;


class UserService extends BaseService
{
    public function __construct(User $user)
    {
        parent::__construct($user);
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
