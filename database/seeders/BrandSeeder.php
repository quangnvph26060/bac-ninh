<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $brands = [];

        for ($i = 1; $i <= 20; $i++) {
            $brands[] = [
                'name'        => 'Thương hiệu ' . $i,
                'logo'        => 'https://via.placeholder.com/150?text=Brand+' . $i, // URL giả lập logo
                'email'       => 'brand' . $i . '@example.com',
                'phone'       => '09' . rand(10000000, 99999999), // Số điện thoại giả
                'address'     => 'Địa chỉ số ' . $i . ', Thành phố ABC',
                'supplier_id' => rand(34, 53), // Giả sử có 10 supplier
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        \DB::table('brands')->insert($brands);
    }
}
