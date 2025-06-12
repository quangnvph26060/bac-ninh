<?php

namespace App\Services;

use App\Models\Supplier;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplierService extends BaseService
{
    public function __construct(public Supplier $supplier)
    {
        parent::__construct($supplier);
    }

    public function pagination()
    {
        $columns = ['id', 'company_name', 'representative_name', 'phone', 'email', 'status', 'bank_account_number', 'bank_id', 'tax_code'];

        return $this->queryBuilder($columns, ['bank']);
    }
    public function show(string $id)
    {
        return $this->findById($id, ['*'], ['brands']);
    }

    public function getAllSupplier(): array
    {
        return $this->pluck(['id', 'company_name']);
    }

    public function create(array $data)
    {
        return transaction(function () use ($data) {
            if (!$supplier = parent::create($data)) {
                return errorResponse('Đã có lỗi xảy ra. Vui lòng thử lại sau!!!');
            }

            if (!empty($data['brand_id'])) {
                $supplier->brands()->sync($data['brand_id']);
            }

            return successResponse('Thêm mới nhà cung cấp thành công.', $supplier);
        });
    }

    public function update(string $id, array $payload)
    {
        return transaction(function () use ($id, $payload) {
            if (!$supplier = $this->updateData($id, $payload)) {
                return errorResponse('Đã có lỗi xảy ra. Vui lòng thử lại sau!!!');
            }

            if (!empty($payload['brand_id'])) {

                $supplier->brands()->sync($payload['brand_id']);
            }

            return successResponse('Lưu thay đổi thành công.');
        });
    }
}
