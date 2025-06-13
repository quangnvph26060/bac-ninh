<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\MaterialRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class MaterialRequestService extends BaseService
{
    public function __construct(
        MaterialRequest $materialRequest,
    ) {
        parent::__construct($materialRequest);
    }

    public function pagination()
    {
        return $this->queryBuilder(
            ['*'],
            ['items', 'creator', 'orderItem.productVariant', 'orderItem.product', 'order'],
        );
    }
}
