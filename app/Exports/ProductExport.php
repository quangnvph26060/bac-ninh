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
        return [
            'ID',
            'Mã sản phẩm',
            'Tên sản phẩm',
            'Số lượng',
            'Giá nhập',
            'Giá bán',
            'Danh mục',
            'Thương hiệu',
            'Đơn vị',
            'Slug',
            'Ảnh',
            'Giá khuyến mãi',
            'Ngày bắt đầu giảm',
            'Ngày kết thúc giảm',
            'Trạng thái kho',
            'Mô tả',
            'Nổi bật',
            'Hiển thị trang chủ',
            'Trạng thái',
            'Tiêu đề SEO',
            'Mô tả SEO',
            'Tags',
        ];
    }

    public function map($product): array
    {
        return [
            $product->id,
            $product->sku,
            $product->name,
            $product->stock,
            $product->import_price,
            $product->sale_price,
            $product->category->name,
            $product->brand->name,
            $product->product_unit,
            $product->slug,
            showImage($product->image),
            $product->discount_price,
            optional($product->discount_start)->format('Y-m-d'),
            optional($product->discount_end)->format('Y-m-d'),
            match ($product->stock_status) {
                'in_stock' => 'Còn hàng',
                'out_of_stock' => 'Hết hàng',
                'waiting_for_goods' => 'Chờ hàng',
                default => 'Không xác định'
            },
            $product->description,
            $product->is_featured ? 'Có' : 'Không',
            $product->is_show_home ? 'Có' : 'Không',
            $product->status == 1 ? 'Xuất bản' : 'Chưa xuất bản',
            $product->seo_title,
            $product->seo_description,
            $product->tags,
        ];
    }
}
