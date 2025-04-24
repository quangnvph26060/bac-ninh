<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = ['male', 'female', 'other'];
        $contractTypes = ['full-time', 'part-time', 'probation'];
        foreach (range(1, 20) as $index) {
            DB::table('employees')->insert([
                'employee_code' => 'NV' . str_pad($index, 3, '0', STR_PAD_LEFT),
                'full_name' => fake()->name,
                'gender' => fake()->randomElement($genders),
                'date_of_birth' => fake()->date('Y-m-d', '-18 years'),
                'phone' => fake()->phoneNumber,
                'email' => fake()->unique()->safeEmail,
                'address' => fake()->address,
                'password' => Hash::make('password'), // mặc định
                'status' => fake()->randomElement([1, 2]),
                'contract_type' => fake()->randomElement($contractTypes),
                'avatar' => 'https://i.pravatar.cc/150?img=' . rand(1, 70), // avatar ngẫu nhiên
                'identity_card_number' => fake()->numerify('##########'),
                'identity_card_image' => 'https://via.placeholder.com/150x100?text=CMND',
                'note' => fake()->optional()->sentence,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
