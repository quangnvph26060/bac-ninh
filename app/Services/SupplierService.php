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

    public function create(array $data) {}

    public function update(string $id, array $payload)
    {
        return transaction(function () use ($id, $payload) {
            if (! $supplier = $this->updateData($id, $payload)) {
                return errorResponse('Đã có lỗi xảy ra. Vui lòng thử lại sau!!!');
            }

            if (!empty($payload['brand_id'])) {
                $supplier->brands()->delete();

                $brands = collect($payload['brand_id'])->map(fn($brand_id) =>  ['brand_id' => $brand_id]);

                $supplier->brands()->createMany($brands);
            }

            return successResponse('Lưu thay đổi thành công.');
        });
    }

    public function deleteSupplier($id)
    {
        DB::beginTransaction();
        try {
            $supplier = Supplier::findOrFail($id); // Tìm người đại diện
            $companyId = $supplier->company_id; // Lưu ID công ty trước khi xóa
            $supplier->delete(); // Xóa người đại diện
            DB::commit();
            return $companyId; // Trả về ID công ty
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete supplier: ' . $e->getMessage());
            throw new Exception('Failed to delete supplier');
        }
    }
}
