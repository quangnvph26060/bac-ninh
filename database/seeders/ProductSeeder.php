<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [];

        for ($i = 1; $i <= 1000; $i++) {
            $products[] = [
                'name'          => 'Sản phẩm ' . $i . time(),
                'code'          => 'SP' . str_pad($i, 3, '0', STR_PAD_LEFT), // SP001, SP002, ...
                'price'         => rand(100000, 500000), // Giá bán ngẫu nhiên
                'priceBuy'      => rand(50000, 400000), // Giá nhập ngẫu nhiên
                'product_unit'  => 'Cái',
                'quantity'      => rand(10, 100), // Số lượng ngẫu nhiên
                'description'   => 'Mô tả sản phẩm ' . $i,
                'is_featured'   => rand(0, 1), // 0 hoặc 1
                'is_new_arrival' => rand(0, 1), // 0 hoặc 1
                'category_id'   => rand(1, 40), // Giả sử có 10 danh mục
                'brand_id'      => rand(61, 105), // Giả sử có 10 thương hiệu
                'status' =>     fake()->randomElement(['inactive', 'published']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }


        \DB::table('products')->insert($products);
    }
}
