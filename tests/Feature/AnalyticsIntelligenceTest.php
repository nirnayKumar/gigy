<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\EarningsGoal;
use App\Models\User;
use App\Services\AnalyticsService;
use App\Services\BenchmarkService;
use App\Services\PlatformInsightsService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AnalyticsIntelligenceTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->user = User::create([
            'name' => 'Karan Malhotra',
            'email' => 'karan_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'worker_type' => 'Food Delivery Partner',
            'city' => 'Bengaluru',
            'preferred_language' => 'English',
            'is_demo' => false,
        ]);
    }

    public function test_unauthenticated_guests_redirected_from_analytics(): void
    {
        $this->get('/analytics')->assertRedirect('/login');
        $this->get('/analytics/benchmark')->assertRedirect('/login');
        $this->get('/analytics/platforms')->assertRedirect('/login');
        $this->get('/analytics/complaints')->assertRedirect('/login');
    }

    public function test_authenticated_worker_can_view_analytics_dashboard(): void
    {
        $response = $this->actingAs($this->user)->get('/analytics');

        $response->assertStatus(200);
        $response->assertSee('Intelligence & Analytics', false);
        $response->assertSee('Net Take-Home', false);
        $response->assertSee('Effective Hourly Rate', false);
        $response->assertSee('earningsOverTimeChart', false);
        $response->assertSee('Hourly Earning Intensity Heatmap', false);
    }

    public function test_analytics_date_range_filters_respond_correctly(): void
    {
        $response7d = $this->actingAs($this->user)->get('/analytics?range=7d');
        $response7d->assertStatus(200);
        $response7d->assertSee('Last 7 Days');

        $response90d = $this->actingAs($this->user)->get('/analytics?range=90d');
        $response90d->assertStatus(200);
        $response90d->assertSee('Last 90 Days');
    }

    public function test_community_benchmark_displays_peer_data_and_enforces_cohort_safety(): void
    {
        $response = $this->actingAs($this->user)->get('/analytics/benchmark');

        $response->assertStatus(200);
        $response->assertSee('Community Benchmark');
        $response->assertSee('Privacy Safeguard Active');

        // Test small cohort suppression rule: filter to non-existent city with 0 workers
        $smallCohortResponse = $this->actingAs($this->user)->get('/analytics/benchmark?city=NonExistentCity');
        $smallCohortResponse->assertStatus(200);
        $smallCohortResponse->assertSee('Cohort Size Below Privacy Threshold');
    }

    public function test_platform_insights_page_displays_companies_and_sorting(): void
    {
        $response = $this->actingAs($this->user)->get('/analytics/platforms');

        $response->assertStatus(200);
        $response->assertSee('Platform Insights');
        $response->assertSee('Highest Median Net Pay');
        $response->assertSee('Lowest Expense Burden');
        $response->assertSee('Best Grievance Resolution');
        $response->assertSee('SwiftDrop');
    }

    public function test_complaint_analytics_displays_trends_and_explorer(): void
    {
        $response = $this->actingAs($this->user)->get('/analytics/complaints');

        $response->assertStatus(200);
        $response->assertSee('Worker Issues & Complaint Trends');
        $response->assertSee('Systemic Dispute Insights');
        $response->assertSee('Anonymized Complaint Explorer');
        $response->assertSee('Resolution Settlement Rate');
    }

    public function test_worker_can_create_and_manage_earnings_goals(): void
    {
        $response = $this->actingAs($this->user)->post('/goals', [
            'type' => 'weekly',
            'target_amount' => 8500.00,
            'notes' => 'Diwali surge incentive push',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('earnings_goals', [
            'user_id' => $this->user->id,
            'type' => 'weekly',
            'target_amount' => 8500.00,
            'status' => 'active',
        ]);

        $goal = EarningsGoal::where('user_id', $this->user->id)->first();
        $this->assertNotNull($goal);
        $this->assertEquals(0.00, $goal->progress_percent);
        $this->assertEquals(8500.00, $goal->remaining_amount);

        // Delete goal
        $deleteResponse = $this->actingAs($this->user)->delete("/goals/{$goal->id}");
        $deleteResponse->assertRedirect();
        $this->assertDatabaseMissing('earnings_goals', ['id' => $goal->id]);
    }

    public function test_analytics_csv_export_streams_correct_data(): void
    {
        // Log a test shift
        $this->user->earningsEntries()->create([
            'work_date' => date('Y-m-d'),
            'gross_earnings' => 1200.00,
            'incentives' => 150.00,
            'fuel_expenses' => 180.00,
            'other_expenses' => 0.00,
            'platform_deductions' => 50.00,
            'hours_worked' => 7.0,
            'deliveries_count' => 14,
        ]);

        $response = $this->actingAs($this->user)->get('/analytics/export?range=30d');

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('Content-Type'));
        $content = $response->streamedContent();
        $this->assertStringContainsString('Gross Earnings (INR)', $content);
        $this->assertStringContainsString('1200', $content);
    }
}
