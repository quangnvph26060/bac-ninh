<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialImport;
use App\Models\MaterialImportDetail;
use App\Models\SupplierPayment;
use App\Models\Type;
use Illuminate\Support\Carbon;

class SupplierPaymentService extends BaseService
{
    public function __construct(
        SupplierPayment $supplierPayment,
    ) {
        parent::__construct($supplierPayment);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'name',
            'code',
            'unit',
            'min_stock',
            'note',
            'created_at',
        ];

        return $this->queryBuilder(
            $columns,
            ['inventory'],
            false,
            ['unit']
        );
    }

    public function getTotalPaid($from = null, $to = null)
    {
        return $this->model->when($from && $to, function ($query) use ($from, $to) {
            $query->whereBetween('created_at', [$from, $to]);
        })->sum('amount');
    }
}
