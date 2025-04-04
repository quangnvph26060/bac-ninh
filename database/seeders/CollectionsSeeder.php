<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CollectionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = [
            [
                'name'        => 'Bộ Sưu Tập Mùa Hè',
                'slug'        => Str::slug('Bộ Sưu Tập Mùa Hè'),
                'description' => 'Bộ sưu tập dành cho mùa hè với phong cách thoải mái, mát mẻ.',
                'status'      => 1, // 1: Hoạt động, 0: Không hoạt động
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'name'        => 'Bộ Sưu Tập Mùa Đông',
                'slug'        => Str::slug('Bộ Sưu Tập Mùa Đông'),
                'description' => 'Bộ sưu tập thời trang giữ ấm cho mùa đông.',
                'status'      => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'name'        => 'Thời Trang Công Sở',
                'slug'        => Str::slug('Thời Trang Công Sở'),
                'description' => 'Các mẫu trang phục thanh lịch, chuyên nghiệp cho dân công sở.',
                'status'      => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'name'        => 'Thời Trang Dạo Phố',
                'slug'        => Str::slug('Thời Trang Dạo Phố'),
                'description' => 'Những bộ trang phục thoải mái, trẻ trung cho các buổi dạo phố.',
                'status'      => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
            [
                'name'        => 'Thời Trang Thể Thao',
                'slug'        => Str::slug('Thời Trang Thể Thao'),
                'description' => 'Trang phục thể thao năng động, phù hợp cho việc tập luyện.',
                'status'      => 1,
                'created_at'  => Carbon::now(),
                'updated_at'  => Carbon::now(),
            ],
        ];

        DB::table('collections')->insert($collections);
    }
}
