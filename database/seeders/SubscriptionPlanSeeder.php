<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name'          => 'Mingguan',
                'duration_days' => 7,
                'price_motor'   => 20000,
                'price_mobil'   => 50000,
                'description'   => 'Cocok untuk yang parkir rutin dalam waktu singkat.',
                'is_popular'    => false,
                'sort_order'    => 1,
            ],
            [
                'name'          => 'Bulanan',
                'duration_days' => 30,
                'price_motor'   => 70000,
                'price_mobil'   => 180000,
                'description'   => 'Paket paling banyak dipilih pengguna.',
                'is_popular'    => true,
                'sort_order'    => 2,
            ],
            [
                'name'          => 'Tahunan',
                'duration_days' => 365,
                'price_motor'   => 700000,
                'price_mobil'   => 1800000,
                'description'   => 'Paling hemat untuk penggunaan jangka panjang.',
                'is_popular'    => false,
                'sort_order'    => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::updateOrCreate(['name' => $plan['name']], $plan);
        }
    }
}