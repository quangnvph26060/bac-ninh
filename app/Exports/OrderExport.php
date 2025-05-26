<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderExport implements FromCollection, WithHeadings
{
    protected array $orderIds;

    public function __construct(array $orderIds = [])
    {
        $this->orderIds = $orderIds;
    }

    public function collection()
    {
        $query = Order::with(['orderItems.productVariant', 'orderItems.product'])
            ->where('user_id', auth()->guard('web')->id());

        if (!empty($this->orderIds)) {
            $query->whereIn('id', $this->orderIds);
        }

        return $query->get()->flatMap(function ($order) {
            return $order->orderItems->map(function ($item) use ($order) {
                return [
                    'order_code' => $order->order_code,
                    'order_name' => $order->order_name,
                    'order_status' => str_replace('_', ' ', $order->status),
                    'payment_status' => $this->mapPaymentStatus($order->payment_status),
                    'product_name' => $item->product->name,
                    'product_type' => $item->product->type,
                    'skuItem' => $item->productVariant->sku ?? $item->product->sku,
                    'quantity' => $item->quantity,
                    'price' => formatPrice($item->price),
                    'shipping_fee' => formatPrice($order->shipping_fee),
                    'tax' => 0,
                    'discount' => formatPrice($order->discount),
                    'surcharge' => formatPrice($order->total - $order->discount - $order->shipping_fee),
                    'amount' => formatPrice($order->total),
                    'full_name' => $order->full_name,
                    'shipping_email' => $order->email ?? 'N/A',
                    'shipping_phone' => $order->phone_number ?? 'N/A',
                    'shipping_address' => $order->shipping_address,
                    'zip_code' => $order->zip_code ?? 'N/A',
                    'tracking_number' => $order->tracking ?? 'N/A',
                    'url_mockup' => showImage($item->model_image) ?? 'N/A',
                    'url_design' => showImage($item->design_image)
                ];
            });
        });
    }

    protected function mapPaymentStatus($status)
    {
        return match ($status) {
            'pending' => 'unpaid',
            'completed' => 'paid',
            'refunded' => 'refunded',
        };
    }



    public function headings(): array
    {
        return [
            'order_code',
            'order_name',
            'order_status',
            'payment_status',
            'product_name',
            'product_type',
            'skuItem',
            'quantity',
            'price',
            'shipping_fee',
            'tax',
            'discount',
            'surcharge',
            'amount',
            'full_name',
            'shipping_email',
            'shipping_phone',
            'shipping_address',
            'zip_code',
            'tracking_number',
            'url_mockup',
            'url_design'
        ];
    }
}
