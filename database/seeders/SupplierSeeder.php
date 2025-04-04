<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $suppliers = [];

        for ($i = 1; $i <= 20; $i++) {
            $suppliers[] = [
                'bank_id'             => rand(1, 63), // Giả sử có 5 ngân hàng
                'company_name'        => 'Công ty TNHH ' . Str::random(8),
                'representative_name' => 'Nguyễn Văn ' . chr(65 + $i), // A, B, C, ...
                'position'            => 'Giám đốc',
                'phone'               => '09' . rand(10000000, 99999999),
                'email'               => 'supplier' . $i . '@example.com',
                'address'             => 'Số ' . rand(1, 100) . ', Đường ' . Str::random(6) . ', Hà Nội',
                'tax_code'            => rand(100000000, 999999999), // 9 số ngẫu nhiên
                'bank_account_number' => rand(1000000000, 9999999999), // 10 số ngẫu nhiên
                'notes'               => 'Ghi chú mẫu ' . $i,
                'status'              => rand(1, 2) ,
                'created_at'          => now(),
                'updated_at'          => now(),
            ];
        }

        \DB::table('suppliers')->insert($suppliers);
    }
}
