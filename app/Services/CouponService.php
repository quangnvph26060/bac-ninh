<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService extends BaseService
{
    public function __construct(Coupon $coupon)
    {
        parent::__construct($coupon);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'code',
            'type',
            'value',
            'min_order_value',
            'max_discount',
            'start_date',
            'end_date',
            'status'
        ];

        return $this->queryBuilder(
            $columns,
        );
    }

    public function store(array $payload)
    {
        return transaction(function () use ($payload) {

            if (!$coupon = $this->create($payload)) {
                errorResponse('Mã giảm giá thất bại!');
            }

            $productIds = collect(explode(',', is_array($payload['product_id']) ? $payload['product_id'][0] : $payload['product_id']))
                ->map(fn($id) => (int) $id)
                ->toArray();

            $coupon->products()->sync($productIds);

            return successResponse('Mã giảm giá thành công.', [], 201);
        });
    }

    public function show(string $id)
    {
        return $this->findById($id, ['*'], ['products']);
    }

    public function update(string $id, array $payload)
    {
        return transaction(function () use ($id, $payload) {

            if (!$coupon = $this->updateData($id, $payload)) {
                errorResponse('Mã giảm giá thất bại!');
            }

            $productIds = collect($payload['product_id'] ?? [])
                ->when(!is_array($payload['product_id']), function ($collection) use ($payload) {
                    return collect(explode(',', $payload['product_id']));
                })
                ->filter(fn($id) => !empty($id)) // Loại bỏ phần tử rỗng
                ->map(fn($id) => (int) $id)
                ->toArray();

            $coupon->products()->sync($productIds);

            return successResponse('Lưu thay đổi thành công.', [], 200);
        });
    }
}
