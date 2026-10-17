<?php

namespace Database\Seeders;

use App\Models\EarningsGoal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoAnalyticsSeeder extends Seeder
{
    /**
     * Run the complete Gigy 2.0 synthetic analytics dataset.
     */
    public function run(): void
    {
        $this->command?->info('Seeding Demo Companies...');
        $this->call(DemoCompanySeeder::class);

        $this->command?->info('Seeding Demo Workers...');
        $this->call(DemoWorkerSeeder::class);

        $this->command?->info('Seeding Work Sessions, Deliveries, and Shifts...');
        $this->call(DemoWorkSessionSeeder::class);

        $this->command?->info('Seeding Grievance Complaints...');
        $this->call(DemoComplaintSeeder::class);

        // Ensure default user has an active earnings goal
        $defaultUser = User::where('email', 'demo_worker@example.com')->first();
        if (!$defaultUser) {
            $defaultUser = User::first();
        }

        if ($defaultUser && EarningsGoal::where('user_id', $defaultUser->id)->count() === 0) {
            EarningsGoal::create([
                'user_id' => $defaultUser->id,
                'type' => 'weekly',
                'target_amount' => 7500.00,
                'start_date' => Carbon::now()->startOfWeek()->toDateString(),
                'end_date' => Carbon::now()->endOfWeek()->toDateString(),
                'status' => 'active',
                'notes' => 'Weekly target for festival surge week',
            ]);
        }

        $this->command?->info('Gigy 2.0 Demo Analytics Seeding Completed Successfully.');
    }
}
