<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Company::factory()->count(10)->create();

        // foreach (\DB::table('products')->get() as $product) {
        //     \DB::table('company_product')->insert([
        //         'product_id' => $product->id,
        //         'company_id' => rand(1, 16)
        //     ]);
        // }
    }
}
