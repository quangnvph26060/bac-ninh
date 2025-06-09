<?php

namespace App\Services;

use App\Models\Type;

class TypeService extends BaseService
{
    public function __construct(Type $type)
    {
        parent::__construct($type);
    }

    public function getTypeNames()
    {
        return $this->model->select('id', 'name')->distinct()->pluck('name', 'id');
    }
}
