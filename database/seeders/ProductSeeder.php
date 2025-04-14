<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $name = fake()->sentence(3);
            $slug = Str::slug($name);
            $importPrice = fake()->numberBetween(10000, 300000);
            $salePrice = $importPrice + fake()->numberBetween(10000, 50000);
            $discountPrice = $salePrice - fake()->numberBetween(5000, 20000);
            $discountStart = fake()->dateTimeBetween('-6 months', 'now');
            $discountEnd = fake()->dateTimeBetween($discountStart, '+1 month');

            DB::table('products')->insert([
                'company_id' => 1,
                'name' => $name,
                'slug' => $slug,
                'image' => 'https://picsum.photos/seed/' . $i . '/600/400',
                'sale_price' => $salePrice,
                'import_price' => $importPrice,
                'discount_price' => $discountPrice,
                'discount_start' => $discountStart,
                'discount_end' => $discountEnd,
                'product_unit' => 'cái',
                'stock' => fake()->numberBetween(0, 500),
                'stock_status' => fake()->randomElement(['in_stock', 'out_of_stock', 'waiting_for_goods']),
                'type' => 'simple',
                'description' => fake()->text(150),
                'is_featured' => fake()->boolean(),
                'is_show_home' => fake()->boolean(),
                'category_id' => fake()->numberBetween(93, 150),
                'cross_sell' => null,
                'status' => 1,
                'brand_id' => fake()->numberBetween(107, 126),
                'seo_title' => fake()->sentence(6),
                'seo_description' => fake()->text(160),
                'tags' => json_encode(fake()->words(3)),
                'created_at' => now(),
                'updated_at' => now(),
                'sku' => 'SP' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
