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

            if (! $this->create($payload)) {
                errorResponse('Mã giảm giá thất bại!');
            }

            return successResponse('Mã giảm giá thành công.', [], 201);
        });
    }

    public function show(string $id)
    {
        return $this->findById($id);
    }

    public function update(string $id, array $payload)
    {
        return transaction(function () use ($id, $payload) {
            if (! $this->updateData($id, $payload)) {
                errorResponse('Mã giảm giá thất bại!');
            }

            return successResponse('Lưu thay đổi thành công.', [], 200);
        });
    }
}
