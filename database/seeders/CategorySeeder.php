<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            \DB::table('categories')->insert([
                'name'            => fake()->word(),
                'slug'            => Str::slug(fake()->unique()->word()),
                'image'           => fake()->imageUrl(200, 200, 'cats'),
                'description'     => fake()->sentence(),
                'seo_title'       => fake()->sentence(3),
                'seo_description' => fake()->text(100),
                'status'          => fake()->boolean(),
                '_lft'            => $i * 2 - 1,
                '_rgt'            => $i * 2,
                'parent_id'       => ($i > 1 && $i % 5 == 0) ? rand(1, $i - 1) : null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}
