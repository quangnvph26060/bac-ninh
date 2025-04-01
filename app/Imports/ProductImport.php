<?php

namespace App\Imports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Importable;
use Illuminate\Support\Collection;

class ProductImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    use Importable;

    public $errors = [];

    /**
     * Chỉ lấy dữ liệu từ sheet đầu tiên
     */
    public function sheets(): array
    {
        return [
            0 => $this, // Sheet đầu tiên (index = 0)
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowIndex = $index + 2; // Vì hàng đầu tiên là tiêu đề, dữ liệu bắt đầu từ hàng 2

            // Validate từng hàng
            $validator = Validator::make(
                $row->toArray(),
                [
                    'ten_san_pham'  => 'required|string|max:255',
                    'ma_san_pham'   => 'required|string|max:50|unique:products,code',
                    'gia_nhap'      => 'required|numeric|min:1000',
                    'gia_ban'       => 'required|numeric|min:1000',
                    'so_luong'      => 'required|integer|min:1',
                    'danh_muc'      => 'required|string|max:255',
                    'thuong_hieu'   => 'required|string|max:255',
                    'don_vi'        => 'required|string|max:255',
                ],
                __('request.messages'),
                [
                    'ten_san_pham' => 'Tên sản phẩm',
                    'ma_san_pham' => 'Mã sản phẩm',
                    'gia_nhap' => 'Giá nhập',
                    'gia_ban' => 'Giá bán',
                    'so_luong' => 'Số lượng',
                    'danh_muc' => 'Danh mục',
                    'thuong_hieu' => 'Thương hiệu',
                    'don_vi' => 'Đơn vị',
                ]
            );

            if ($validator->fails()) {
                $this->errors[] = "Lỗi tại hàng $rowIndex: " . $row['ten_san_pham'] . " - " . implode(', ', $validator->errors()->all());
                continue;
            }

            // Kiểm tra sản phẩm đã tồn tại chưa
            $exists = Product::where('code', $row['ma_san_pham'])->orWhere('name', $row['ten_san_pham'])->exists();
            if ($exists) {
                continue;
            }

            // Lấy hoặc tạo danh mục
            $category = Category::firstOrCreate(
                ['name' => trim($row['danh_muc'])],
                ['created_at' => now(), 'updated_at' => now(), 'slug' => Str::slug($row['danh_muc'])]
            );

            // Lấy hoặc tạo thương hiệu
            $brand = Brand::firstOrCreate(
                ['name' => trim($row['thuong_hieu'])],
                ['created_at' => now(), 'updated_at' => now(), 'slug' => Str::slug($row['thuong_hieu'])]
            );

            // Thêm sản phẩm mới
            Product::create([
                'name'        => trim($row['ten_san_pham']),
                'code'        => trim($row['ma_san_pham']),
                'price'       => (int) $row['gia_nhap'],
                'priceBuy'    => (int) $row['gia_ban'],
                'quantity'    => (int) $row['so_luong'],
                'category_id' => $category->id,
                'brand_id'    => $brand->id,
                'status'      => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
