<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Complaint;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoComplaintSeeder extends Seeder
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

        if (Complaint::where('is_demo', true)->count() > 50) {
            return;
        }

        $categories = [
            'Incentives not credited',
            'Unfair penalties',
            'Payment delays',
            'Incorrect earnings calculations',
            'App or technical issues',
            'Support response delays',
            'Customer disputes',
            'Account suspension',
            'Safety concerns',
        ];

        $now = Carbon::now();

        // Generate ~180 realistic complaints across the 90 days
        for ($i = 1; $i <= 180; $i++) {
            $worker = $workers->random();
            $company = $companies->random();
            $category = $categories[array_rand($categories)];
            $incidentDaysAgo = rand(2, 85);
            $incidentDate = $now->copy()->subDays($incidentDaysAgo);

            // Distribution of statuses: 55% resolved, 25% submitted (open), 12% draft, 8% cancelled
            $randStatus = rand(1, 100);
            if ($randStatus <= 55) {
                $status = 'resolved';
                $resolutionHours = rand(12, 96);
                $resolvedAt = $incidentDate->copy()->addHours($resolutionHours);
            } elseif ($randStatus <= 80) {
                $status = 'submitted';
                $resolutionHours = null;
                $resolvedAt = null;
            } elseif ($randStatus <= 92) {
                $status = 'draft';
                $resolutionHours = null;
                $resolvedAt = null;
            } else {
                $status = 'cancelled';
                $resolutionHours = rand(6, 48);
                $resolvedAt = $incidentDate->copy()->addHours($resolutionHours);
            }

            $expected = match ($category) {
                'Incentives not credited' => round(rand(200, 900), 2),
                'Unfair penalties' => round(rand(100, 450), 2),
                'Payment delays' => round(rand(800, 3200), 2),
                'Incorrect earnings calculations' => round(rand(150, 600), 2),
                default => null,
            };

            $received = ($expected !== null && $category === 'Incentives not credited')
                ? round($expected - rand(150, 400), 2)
                : 0.00;

            Complaint::create([
                'user_id' => $worker->id,
                'company_name' => $company->name,
                'issue_category' => $category,
                'expected_amount' => $expected,
                'received_amount' => $received,
                'incident_date' => $incidentDate->toDateString(),
                'description' => "Synthetic grievance report for {$category} logged with {$company->name} partner support in {$worker->city}.",
                'preferred_language' => $worker->preferred_language ?? 'English',
                'generated_draft' => "Subject: Formal Dispute regarding {$category} - Ref ID {$i}\n\nDear {$company->name} Grievance Cell,\n\nI am logging a formal dispute regarding {$category} that occurred on {$incidentDate->toDateString()}. Expected compensation is ₹{$expected}.\n\nSincerely,\n{$worker->name}",
                'status' => $status,
                'city' => $worker->city,
                'worker_role' => $worker->worker_type,
                'resolution_time_hours' => $resolutionHours,
                'resolved_at' => $resolvedAt,
                'is_demo' => true,
                'created_at' => $incidentDate->toDateTimeString(),
                'updated_at' => ($resolvedAt ?: $incidentDate)->toDateTimeString(),
            ]);
        }
    }
}
