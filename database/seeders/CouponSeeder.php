<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Coupon::firstOrCreate(
            ['code' => 'SAVE10'],
            [
                'type' => 'percent',
                'value' => 10,
                'max_discount' => 200,
                'min_spend' => 500,
                'usage_limit' => 1000,
                'usage_limit_per_user' => 3,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(6),
                'active' => true,
            ]
        );

        \App\Models\Coupon::firstOrCreate(
            ['code' => 'LESS50'],
            [
                'type' => 'fixed',
                'value' => 50,
                'min_spend' => 0,
                'usage_limit' => null,
                'usage_limit_per_user' => null,
                'starts_at' => now()->subDay(),
                'ends_at' => now()->addMonths(3),
                'active' => true,
            ]
        );
    }
}
