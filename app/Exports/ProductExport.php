<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductExport implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Product::with('brand', 'category')->get();
    }

    public function headings(): array
    {
        return ['ID', 'Mã sản phẩm', 'Tên sản phẩm', 'Số lượng', 'Giá nhập', 'Giá bán', 'Danh mục', 'Thương hiệu',  'Đơn vị'];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->code,
            $product->name,
            $product->quantity,
            $product->price,
            $product->priceBuy,
            $product->category->name ?? 'N/A',
            $product->brand->name ?? 'N/A',
            $product->product_unit,
        ];
    }
}
