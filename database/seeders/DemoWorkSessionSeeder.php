<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Delivery;
use App\Models\EarningsEntry;
use App\Models\User;
use App\Models\WorkSession;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoWorkSessionSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(42);

        $companies = Company::where('is_demo', true)->get();
        if ($companies->isEmpty()) {
            $this->call(DemoCompanySeeder::class);
            $companies = Company::where('is_demo', true)->get();
        }

        $workers = User::where('is_demo', true)->get();
        if ($workers->isEmpty()) {
            $this->call(DemoWorkerSeeder::class);
            $workers = User::where('is_demo', true)->get();
        }

        // Only generate if we don't already have demo work sessions
        if (WorkSession::where('is_demo', true)->count() > 100) {
            return;
        }

        $now = Carbon::now();

        // Sample a subset of workers for detailed 90-day activity (e.g. 40 workers x 25 shifts = 1,000 shifts)
        $activeWorkers = $workers->take(50);

        foreach ($activeWorkers as $worker) {
            // Assign a primary company and secondary company
            $primaryCompany = $companies->random();

            // Generate ~20 shifts across last 60 days
            for ($d = 60; $d >= 0; $d -= rand(2, 4)) {
                $shiftDate = $now->copy()->subDays($d);
                
                // Peak lunch (11:30) or peak dinner (18:00)
                $isDinner = (rand(1, 10) > 4);
                $startHour = $isDinner ? 18 : 11;
                $startMinute = rand(0, 30);
                $startTime = $shiftDate->copy()->setTime($startHour, $startMinute, 0);

                $hours = rand(4, 8) + (rand(0, 3) * 0.25); // e.g. 5.5 hours
                $endTime = $startTime->copy()->addMinutes((int) ($hours * 60));

                // Company specific multiplier
                $baseRatePerHour = match ($primaryCompany->slug) {
                    'swiftdrop' => rand(130, 175),
                    'quickkart' => rand(140, 185),
                    'cityrunner' => rand(125, 160),
                    'flashbyte' => rand(135, 180),
                    'urbanride' => rand(160, 220),
                    default => 140,
                };

                $deliveriesCount = (int) round($hours * rand(2, 3));
                $gross = round($hours * $baseRatePerHour, 2);
                $incentives = (rand(1, 10) > 4) ? round(rand(80, 250), 2) : 0.00;
                $fuel = round($hours * rand(20, 35), 2);
                $deductions = (rand(1, 10) > 8) ? round(rand(30, 90), 2) : 0.00;
                $other = (rand(1, 10) > 8) ? round(rand(10, 40), 2) : 0.00;

                $totalIncome = $gross + $incentives;
                $totalExpenses = $fuel + $deductions + $other;
                $net = round($totalIncome - $totalExpenses, 2);
                $hourlyRate = round($net / $hours, 2);

                // 1. Create EarningsEntry for this worker
                $earningsEntry = EarningsEntry::create([
                    'user_id' => $worker->id,
                    'work_date' => $shiftDate->toDateString(),
                    'company_name' => $primaryCompany->name,
                    'city' => $worker->city,
                    'worker_role' => $worker->worker_type,
                    'deliveries_count' => $deliveriesCount,
                    'gross_earnings' => $gross,
                    'incentives' => $incentives,
                    'fuel_expenses' => $fuel,
                    'other_expenses' => $other,
                    'platform_deductions' => $deductions,
                    'hours_worked' => $hours,
                    'notes' => "{$primaryCompany->name} shift in {$worker->city}",
                    'is_demo' => true,
                ]);

                // 2. Create WorkSession
                $session = WorkSession::create([
                    'user_id' => $worker->id,
                    'company_id' => $primaryCompany->id,
                    'earnings_entry_id' => $earningsEntry->id,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'total_hours' => $hours,
                    'deliveries_completed' => $deliveriesCount,
                    'gross_earnings' => $totalIncome,
                    'net_earnings' => $net,
                    'hourly_rate' => $hourlyRate,
                    'city' => $worker->city,
                    'worker_role' => $worker->worker_type,
                    'is_demo' => true,
                ]);

                // 3. Create sample deliveries for session (3 to 6 delivery records per session)
                $deliveryBatch = [];
                $sampleDelivs = min($deliveriesCount, rand(3, 5));
                $currentTime = $startTime->copy();

                for ($k = 1; $k <= $sampleDelivs; $k++) {
                    $duration = rand(15, 35);
                    $dropoff = $currentTime->copy()->addMinutes($duration);
                    $dist = round(rand(18, 75) / 10, 2); // 1.8 to 7.5 km
                    $fare = round(rand(35, 95) + ($dist * 8), 2);
                    $tip = (rand(1, 10) > 7) ? round(rand(10, 40), 2) : 0.00;
                    $status = (rand(1, 100) > 4) ? 'completed' : 'cancelled';

                    $deliveryBatch[] = [
                        'work_session_id' => $session->id,
                        'user_id' => $worker->id,
                        'company_id' => $primaryCompany->id,
                        'delivery_identifier' => strtoupper(substr($primaryCompany->slug, 0, 3)) . '-' . rand(100000, 999999),
                        'pickup_time' => $currentTime->toDateTimeString(),
                        'dropoff_time' => ($status === 'completed') ? $dropoff->toDateTimeString() : null,
                        'duration_minutes' => $duration,
                        'distance_km' => $dist,
                        'earnings' => ($status === 'completed') ? $fare : 0.00,
                        'tip_amount' => ($status === 'completed') ? $tip : 0.00,
                        'status' => $status,
                        'city' => $worker->city,
                        'is_demo' => true,
                        'created_at' => $startTime->toDateTimeString(),
                        'updated_at' => $endTime->toDateTimeString(),
                    ];

                    $currentTime->addMinutes($duration + rand(5, 15));
                }

                if (!empty($deliveryBatch)) {
                    Delivery::insert($deliveryBatch);
                }
            }
        }
    }
}
