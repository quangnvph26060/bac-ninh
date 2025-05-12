<?php

namespace App\Services;

use App\Models\Order;

class OrderService extends BaseService
{
    public function __construct(Order $order)
    {
        parent::__construct($order);
    }

    public function pagination()
    {
        $columns = [
            'id',
            'order_code',
            'full_name',
            'phone_number',
            'email',
            'total',
            'status',
            'payment_status',
            'payment_method',
            'created_at'
        ];

        return $this->queryBuilder(
            $columns,
            ['orderItems'],
            false,
            [],
            [['status', '<>', 'draft']],
        );
    }

    public function getItemsByOrderCode($code)
    {

        $order = $this->firstdByWhere(['*'], [['order_code', $code]], ['orderItems.productVariant.attributeValues']);

        return [
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'name' => $item->product->name,
                    'image' => showImage($item->product->image),
                    'model_image' => showImage($item->model_image),
                    'design_image' => showImage($item->design_image),
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'total' => $item->quantity * $item->price,
                    'variant' => implode(' - ', $item->productVariant?->attributeValues->pluck('value')->toArray() ?? [])
                ];
            })->toArray(),
            'sub_total' => $order->orderItems->sum(fn($i) => $i->quantity * $i->price),
            'shipping_fee' => $order->shipping_fee,
            'discount' => $order->discount,
            'total' => $order->total,
        ];
    }

    public function show($id)
    {
        return $this->findById($id, ['*'], ['orderItems.productVariant.attributeValues', 'user']);
    }

    public function updateStatus(string $id, $status)
    {
        try {
            $order = $this->findById($id);

            if (!$order) {
                return errorResponse("Order not found in the system!", false, 404);
            }

            if ($this->updateData($id, ['status' => $status])) return successResponse("Status update successful");
        } catch (\Exception $e) {
            logger('error: ' . $e->getMessage());
            return errorResponse("An error occurred, please try again later!", false, 500);
        }
    }
}
