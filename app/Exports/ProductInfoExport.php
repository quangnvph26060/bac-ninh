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
                    'Design width' => $variant->design_width ?? 'N/A',
                    'Design height' => $variant->design_height  ?? 'N/A',
                    'Design ppi' => $variant->design_ppi  ?? 'N/A',
                    'Design format' => $variant->design_format  ?? 'N/A',
                ]);
            }

            // Nếu không có biến thể, vẫn hiển thị 1 dòng
            if ($product->variants->isEmpty()) {
                $data->push([
                    '#' => $index++,
                    'Product Name' => $product->name,
                    'Product Sku' => $product->sku,
                    'Product Variant Sku' => 'N/A',
                    'Design width' =>  'N/A',
                    'Design height' =>  'N/A',
                    'Design ppi' =>  'N/A',
                    'Design format' =>  'N/A',
                ]);
            }
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            '#',
            'Product Name',
            'Product Sku',
            'Product Variant Sku',
            'Design width',
            'Design height',
            'Design ppi',
            'Design format',
        ];
    }
}
