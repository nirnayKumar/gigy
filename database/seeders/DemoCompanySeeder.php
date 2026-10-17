<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Seeder;

class DemoCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            [
                'name' => 'SwiftDrop',
                'slug' => 'swiftdrop',
                'category' => 'Food Delivery',
                'color' => '#059669',
                'is_demo' => true,
            ],
            [
                'name' => 'QuickKart',
                'slug' => 'quickkart',
                'category' => 'Quick Commerce',
                'color' => '#d97706',
                'is_demo' => true,
            ],
            [
                'name' => 'CityRunner',
                'slug' => 'cityrunner',
                'category' => 'Courier & Parcels',
                'color' => '#2563eb',
                'is_demo' => true,
            ],
            [
                'name' => 'FlashByte',
                'slug' => 'flashbyte',
                'category' => 'Quick Commerce',
                'color' => '#9333ea',
                'is_demo' => true,
            ],
            [
                'name' => 'UrbanRide',
                'slug' => 'urbanride',
                'category' => 'Ride-hail Cab/Auto',
                'color' => '#4f46e5',
                'is_demo' => true,
            ],
        ];

        foreach ($companies as $comp) {
            Company::updateOrCreate(
                ['slug' => $comp['slug']],
                $comp
            );
        }
    }
}
