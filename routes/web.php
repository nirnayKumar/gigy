<?php

use App\Http\Controllers\AssistantController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EarningsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home / Landing Route
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth-attempts');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Earnings Tracker
    Route::get('/earnings', [EarningsController::class, 'index'])->name('earnings.index');
    Route::get('/earnings/create', [EarningsController::class, 'create'])->name('earnings.create');
    Route::post('/earnings', [EarningsController::class, 'store'])->name('earnings.store');
    Route::get('/earnings/{earningsEntry}', [EarningsController::class, 'show'])->name('earnings.show');
    Route::post('/earnings/{earningsEntry}/explain', [EarningsController::class, 'explain'])
        ->name('earnings.explain')
        ->middleware('throttle:ai-generation');
    Route::delete('/earnings/{earningsEntry}', [EarningsController::class, 'destroy'])->name('earnings.destroy');

    // Worker Assistant AI Chat
    Route::get('/assistant', [AssistantController::class, 'index'])->name('assistant.index');
    Route::post('/assistant', [AssistantController::class, 'store'])->name('assistant.store');
    Route::get('/assistant/{conversation}', [AssistantController::class, 'show'])->name('assistant.show');
    Route::post('/assistant/{conversation}/message', [AssistantController::class, 'sendMessage'])
        ->name('assistant.message')
        ->middleware('throttle:ai-generation');
    Route::delete('/assistant/{conversation}', [AssistantController::class, 'destroy'])->name('assistant.destroy');

    // Complaints Generator
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::get('/complaints/create', [ComplaintController::class, 'create'])->name('complaints.create');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::post('/complaints/generate-draft', [ComplaintController::class, 'generateDraft'])
        ->name('complaints.generate-draft')
        ->middleware('throttle:ai-generation');
    Route::get('/complaints/{complaint}', [ComplaintController::class, 'show'])->name('complaints.show');
    Route::get('/complaints/{complaint}/edit', [ComplaintController::class, 'edit'])->name('complaints.edit');
    Route::put('/complaints/{complaint}', [ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/complaints/{complaint}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');

    // Gigy 2.0 Analytics & Intelligence Platform
    Route::get('/analytics', [\App\Http\Controllers\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export', [\App\Http\Controllers\AnalyticsController::class, 'exportCsv'])->name('analytics.export');
    Route::get('/analytics/benchmark', [\App\Http\Controllers\BenchmarkController::class, 'index'])->name('analytics.benchmark');
    Route::get('/analytics/platforms', [\App\Http\Controllers\PlatformInsightsController::class, 'index'])->name('analytics.platforms');
    Route::get('/analytics/complaints', [\App\Http\Controllers\ComplaintAnalyticsController::class, 'index'])->name('analytics.complaints');

    // Goals Management
    Route::post('/goals', [\App\Http\Controllers\GoalController::class, 'store'])->name('goals.store');
    Route::delete('/goals/{goal}', [\App\Http\Controllers\GoalController::class, 'destroy'])->name('goals.destroy');
});

// Internal Diagnostics & Verification Suite (Local Development Only)
Route::get('/internal/run-tests', function (\Illuminate\Http\Request $request) {
    abort_unless(app()->isLocal(), 404);
    try {
        $step = $request->query('step', 'all');
        $results = [];
        $gemini = app(\App\Services\GeminiService::class);

        // Setup base test user if not existing
        $user1 = \App\Models\User::firstOrCreate(
            ['email' => 'demo_worker@example.com'],
            [
                'name' => 'Rajesh Sharma',
                'password' => \Illuminate\Support\Facades\Hash::make('Secret123!'),
                'worker_type' => 'Food Delivery (Zomato/Swiggy)',
                'preferred_language' => 'English',
            ]
        );

        if ($step === 'check_env') {
            return response()->json([
                'php_binary' => PHP_BINARY,
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'db_connected' => \Illuminate\Support\Facades\DB::connection()->getPdo() ? true : false,
                'companies_count' => \App\Models\Company::count(),
                'workers_count' => \App\Models\User::where('is_demo', true)->count(),
                'sessions_count' => \App\Models\WorkSession::count(),
                'deliveries_count' => \App\Models\Delivery::count(),
                'complaints_count' => \App\Models\Complaint::count(),
                'goals_count' => \App\Models\EarningsGoal::count(),
            ]);
        }

        if ($step === 'run_artisan') {
            $binary = PHP_BINARY;
            $artisan = base_path('artisan');
            $base = base_path();
            $artisanCmd = escapeshellcmd($request->query('cmd', 'migrate --force'));
            $cmd = "cd /d \"{$base}\" && \"{$binary}\" \"{$artisan}\" {$artisanCmd} 2>&1";
            exec($cmd, $output, $returnCode);
            return response()->json([
                'cmd' => $cmd,
                'return_code' => $returnCode,
                'output' => $output,
            ]);
        }

        if ($step === 'run_phpunit') {
            $binary = PHP_BINARY;
            $phpunit = base_path('vendor/phpunit/phpunit/phpunit');
            $base = base_path();
            $filter = $request->query('filter', '');
            $cmd = "cd /d \"{$base}\" && \"{$binary}\" \"{$phpunit}\" {$filter} 2>&1";
            exec($cmd, $output, $returnCode);
            return response()->json([
                'cmd' => $cmd,
                'return_code' => $returnCode,
                'output' => $output,
            ]);
        }

        if ($step === 'calc' || $step === 'all_fast') {
            // Test 1: Record shift and verify exact calculations
            $entry = $user1->earningsEntries()->create([
                'work_date' => date('Y-m-d'),
                'gross_earnings' => 1000.00,
                'incentives' => 100.00,
                'fuel_expenses' => 150.00,
                'other_expenses' => 0.00,
                'platform_deductions' => 50.00,
                'hours_worked' => 8.0,
                'notes' => '8-hour evening shift with rain surge',
            ]);

            $totalIncome = $entry->total_income;
            $totalExpenses = $entry->total_expenses;
            $netEarnings = $entry->net_earnings;
            $hourlyRate = $entry->effective_hourly_earnings;

            $results['calculations_test'] = [
                'status' => ($totalIncome == 1100.00 && $totalExpenses == 200.00 && $netEarnings == 900.00 && $hourlyRate == 112.50) ? 'PASS' : 'FAIL',
                'total_income' => $totalIncome, // Expected 1100.00
                'total_expenses' => $totalExpenses, // Expected 200.00
                'net_earnings' => $netEarnings, // Expected 900.00
                'effective_hourly_earnings' => $hourlyRate, // Expected 112.50
                'entry_id' => $entry->id,
            ];

            // Cross-user isolation check
            $user2 = \App\Models\User::firstOrCreate(
                ['email' => 'worker_simran@example.com'],
                [
                    'name' => 'Simranjit Kaur',
                    'password' => \Illuminate\Support\Facades\Hash::make('Secret123!'),
                    'worker_type' => 'Ride-hail Cab/Auto',
                    'preferred_language' => 'Punjabi',
                ]
            );

            $results['cross_user_isolation'] = [
                'status' => (!$user2->earningsEntries()->where('id', $entry->id)->exists()) ? 'PASS' : 'FAIL',
                'user1' => $user1->email,
                'user2' => $user2->email,
                'isolated' => true,
            ];
        }

        if ($step === 'explain') {
            $entry = $user1->earningsEntries()->latest()->first();
            if (!$entry) {
                $entry = $user1->earningsEntries()->create([
                    'work_date' => date('Y-m-d'),
                    'gross_earnings' => 1000.00,
                    'incentives' => 100.00,
                    'fuel_expenses' => 150.00,
                    'other_expenses' => 0.00,
                    'platform_deductions' => 50.00,
                    'hours_worked' => 8.0,
                ]);
            }
            $explanation = $gemini->explainEarnings($entry, 'English');
            $results['gemini_earnings_explainer'] = [
                'status' => !empty($explanation) ? 'PASS' : 'FAIL',
                'explanation' => $explanation,
            ];
        }

        if ($step === 'chat') {
            $conv = $user1->assistantConversations()->firstOrCreate(['title' => 'Incentive Question']);
            $aiReply = $gemini->assistantChat($conv, 'Why was my peak hour incentive deducted and how to avoid it?', 'English');
            $conv->messages()->create(['role' => 'user', 'content' => 'Why was my peak hour incentive deducted and how to avoid it?']);
            $conv->messages()->create(['role' => 'assistant', 'content' => $aiReply]);

            $results['gemini_assistant_chat'] = [
                'status' => !empty($aiReply) ? 'PASS' : 'FAIL',
                'conversation_id' => $conv->id,
                'ai_reply' => $aiReply,
            ];
        }

        if ($step === 'complaint') {
            $complaintDraft = $gemini->generateComplaintDraft([
                'company_name' => 'Zomato',
                'issue_category' => 'Unfair Platform Deduction',
                'expected_amount' => 1100.00,
                'received_amount' => 900.00,
                'incident_date' => date('Y-m-d'),
                'description' => 'Target bonus of ₹100 was missing and ₹50 deduction was applied incorrectly.',
            ], 'English');

            $complaint = $user1->complaints()->create([
                'company_name' => 'Zomato',
                'issue_category' => 'Unfair Platform Deduction',
                'expected_amount' => 1100.00,
                'received_amount' => 900.00,
                'incident_date' => date('Y-m-d'),
                'description' => 'Target bonus of ₹100 was missing and ₹50 deduction was applied incorrectly.',
                'preferred_language' => 'English',
                'generated_draft' => $complaintDraft,
                'status' => 'draft',
            ]);

            $results['gemini_complaint_generator'] = [
                'status' => (!empty($complaintDraft) && $complaint->id > 0) ? 'PASS' : 'FAIL',
                'complaint_id' => $complaint->id,
                'draft' => $complaintDraft,
            ];
        }

        return response()->json([
            'step' => $step,
            'results' => $results,
        ], 200);

    } catch (\Throwable $e) {
        return response()->json([
            'status' => 'ERROR',
            'error' => $e->getMessage(),
        ], 200);
    }
});
