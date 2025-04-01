<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'name'        => 'Nhà cung cấp ' . $i,
                'email'       => 'supplier' . $i . '@example.com',
                'phone'       => '09' . rand(10000000, 99999999), // Số điện thoại giả
                'company_id'  => rand(1, 5), // Giả sử có 5 công ty
                'created_at'  => now(),
                'updated_at'  => now(),
            ];
        }

        \DB::table('suppliers')->insert($suppliers);
    }
}
