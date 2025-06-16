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
                    'product_name' => $item->product->name,
                    'product_variant_sku' => $item->productVariant->sku ?? $item->product->sku,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'shipping_fee' => $order->shipping_fee,
                    'tax' => $order->tax,
                    'discount' => $order->discount,
                    'surcharge' => $order->total - $order->discount - $order->shipping_fee,
                    'amount' => $order->total,
                    'first_name' => $order->first_name,
                    'last_name' => $order->last_name,
                    'email' => $order->email ?? 'N/A',
                    'phone' => $order->phone_number ?? 'N/A',
                    'nation' => $order->nation ?? '',
                    'state' => $order->state ?? '',
                    'city' => $order->city ?? '',
                    'street_address' => $order->shipping_address ?? '',
                    'zip_code' => $order->zip_code ?? 'N/A',
                    'note' => $order->note ?? '',
                    'order_status' => str_replace('_', ' ', $order->status),
                    'payment_status' => $this->mapPaymentStatus($order->payment_status),
                    'delivery_method' => $order->delivery_method,
                    'tracking_number' => $order->tracking ?? 'N/A',
                    'mockup_image' => showImage($item->model_image) ?? 'N/A',
                    'design_image' => showImage($item->design_image) ?? 'N/A',
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
            default => 'unknown',
        };
    }

    public function headings(): array
    {
        return [
            'Order Code',
            'Order Name',
            'Product Name',
            'Product Variant SKU',
            'Price',
            'Quantity',
            'Shipping Fee',
            'Tax',
            'Discount',
            'Surcharge',
            'Amount',
            'First Name',
            'Last Name',
            'Email',
            'Phone',
            'Nation',
            'State',
            'City',
            'Street Address',
            'Zip Code',
            'Note',
            'Order Status',
            'Payment Status',
            'Delivery Method',
            'Tracking Number',
            'Mockup Image',
            'Design Image',
        ];
    }
}
