<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrderImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Order Code',        // Mã đơn
            'Order Name',        // Tên đơn
            'Product Name',      // Tên sản phẩm
            'SKU',               // SKU
            'Quantity',          // Số lượng
            'Mockup URL',        // Mockup
            'Design URL',        // Thiết kế
            'First Name',        // Họ
            'Last Name',         // Tên
            'Email',             // Email
            'Phone Number',      // SĐT
            'Province',          // Tỉnh
            'District',          // Huyện
            'Ward',              // Xã
            'Address',           // Địa chỉ
            'Zip Code',          // Zip code
            'Note',              // Ghi chú
            'Delivery Method'    // Phương thức giao
        ];
    }

    public function array(): array
    {
        return [
            [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ]
        ];
    }
}
