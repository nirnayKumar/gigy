<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoWorkerSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(42);

        $firstNames = ['Aarav', 'Vivaan', 'Aditya', 'Vihaan', 'Arjun', 'Sai', 'Reyansh', 'Ayaan', 'Krishna', 'Ishaan', 'Shaurya', 'Atharv', 'Rohan', 'Amit', 'Sunil', 'Rajesh', 'Suresh', 'Deepak', 'Manoj', 'Pooja', 'Ananya', 'Diya', 'Sneha', 'Simran', 'Kavita', 'Neha', 'Priya', 'Priyanka', 'Sunita', 'Manpreet', 'Harpreet', 'Jaswinder', 'Gurpreet', 'Balwinder'];
        $lastNames = ['Sharma', 'Verma', 'Gupta', 'Patel', 'Kumar', 'Singh', 'Yadav', 'Mishra', 'Pandey', 'Joshi', 'Chauhan', 'Reddy', 'Rao', 'Nair', 'Pillai', 'Menon', 'Kaur', 'Gill', 'Dhillon', 'Sandhu', 'Khan', 'Ansari', 'Shaikh'];
        
        $cities = ['Bengaluru', 'Delhi NCR', 'Mumbai', 'Hyderabad'];
        $roles = [
            'Food Delivery Partner',
            'Quick-Commerce Courier',
            'Courier & Parcel Rider',
            'Ride-hail Driver',
        ];
        $languages = ['English', 'Hindi', 'Punjabi'];

        $hashedPassword = Hash::make('DemoPassword123!');

        for ($i = 1; $i <= 100; $i++) {
            $fname = $firstNames[array_rand($firstNames)];
            $lname = $lastNames[array_rand($lastNames)];
            $city = $cities[$i % count($cities)];
            $role = $roles[$i % count($roles)];
            $lang = $languages[$i % count($languages)];

            User::updateOrCreate(
                ['email' => "demo_worker_{$i}@example.com"],
                [
                    'name' => "{$fname} {$lname}",
                    'password' => $hashedPassword,
                    'worker_type' => $role,
                    'city' => $city,
                    'preferred_language' => $lang,
                    'is_demo' => true,
                ]
            );
        }
    }
}
