<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialImport;
use App\Models\MaterialImportDetail;
use App\Models\SupplierDebt;
use Illuminate\Support\Carbon;

class SupplierDebtService extends BaseService
{
    public function __construct(
        SupplierDebt $supplierDebt
    ) {
        parent::__construct($supplierDebt);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'code',
            'supplier_id',
            'material_import_id',
            'total_amount',
            'paid_amount',
            'status',
            'note',
            'created_at'
        ];

        return $this->queryBuilder(
            $columns,
            ['supplier', 'import'],
            false,
            ['supplier_id'],
            [['status', '<>', 'paid']],
            [],
            [],
            [],
            [],
        );
    }

    public function show($id)
    {
        return $this->findById($id, ['*'], ['supplier', 'import.details.material', 'import.debt.payments.employee', 'import.debt.payments' => function ($q) {
            $q->orderBy('date');
        }]);
    }

    public function getTotalDebt($from = null, $to = null)
    {
        $query = $this->model->newQuery();

        if ($from && $to) {
            $query->whereBetween('created_at', [$from, $to]);
        }

        return $query->sum('total_amount');
    }


    public function pay($data)
    {
        return transaction(function () use ($data) {
            $supplierDebt = $this->findById($data['debt_id']);

            $remainingAmount = $supplierDebt->total_amount - $supplierDebt->paid_amount;

            if ($remainingAmount < $data['amount']) {
                return errorResponse("Số tiền thanh toán phải nhỏ hơn hoặc bằng '" . number_format($remainingAmount, 2) . "' USD");
            }

            // Tạo bản ghi thanh toán
            $supplierDebt->payments()->create([
                'date' => $data['date'],
                'amount' => $data['amount'],
                'note' => $data['note'],
                'created_by' => auth('admin')->id()
            ]);

            // Cập nhật số tiền đã thanh toán
            $newPaidAmount = $supplierDebt->paid_amount + $data['amount'];
            $supplierDebt->paid_amount = $newPaidAmount;

            // Tính trạng thái mới
            $total = $supplierDebt->total_amount;
            $status = match (true) {
                $newPaidAmount == 0      => 'unpaid',
                $newPaidAmount < $total  => 'partial',
                $newPaidAmount >= $total => 'paid',
                default                  => 'unpaid',
            };

            $supplierDebt->status = $status;
            $supplierDebt->save();

            return successResponse("Thanh toán thành công.");
        });
    }
}
