<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\ProductVariant;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductInfoExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        $data = collect();
        $index = 1;

        $products = Product::with('variants')->get();

        foreach ($products as $product) {
            foreach ($product->variants as $variant) {
                $data->push([
                    '#' => $index++,
                    'Product Name' => $product->name,
                    'Product Sku' => $product->sku,
                    'Product Variant Sku' => $variant->sku,
                ]);
            }

            // Nếu không có biến thể, vẫn hiển thị 1 dòng
            if ($product->variants->isEmpty()) {
                $data->push([
                    '#' => $index++,
                    'Product Name' => $product->name,
                    'Product Sku' => $product->sku,
                    'Product Variant Sku' => 'N/A',
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return ['#', 'Product Name', 'Product Sku', 'Product Variant Sku'];
    }
}
