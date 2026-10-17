<?php

namespace Tests\Feature;

use App\Models\AssistantConversation;
use App\Models\Complaint;
use App\Models\EarningsEntry;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityAndAuthorizationTest extends TestCase
{
    private User $userA;
    private User $userB;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->userA = User::create([
            'name' => 'Worker Alice',
            'email' => 'alice_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'worker_type' => 'Food Delivery (Zomato/Swiggy)',
            'preferred_language' => 'English',
        ]);

        $this->userB = User::create([
            'name' => 'Worker Bob',
            'email' => 'bob_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'worker_type' => 'Ride-hail Cab/Auto',
            'preferred_language' => 'Hindi',
        ]);
    }

    public function test_unauthenticated_guests_are_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/earnings')->assertRedirect('/login');
        $this->get('/complaints')->assertRedirect('/login');
        $this->get('/assistant')->assertRedirect('/login');
    }

    public function test_user_cannot_view_another_users_earnings(): void
    {
        $entryA = EarningsEntry::create([
            'user_id' => $this->userA->id,
            'work_date' => date('Y-m-d'),
            'gross_earnings' => 1000.00,
            'incentives' => 100.00,
            'platform_deductions' => 50.00,
            'fuel_expenses' => 150.00,
            'other_expenses' => 0.00,
            'hours_worked' => 8.0,
            'total_income' => 1100.00,
            'total_expenses' => 200.00,
            'net_earnings' => 900.00,
            'effective_hourly_earnings' => 112.50,
        ]);

        // User B attempts to access User A's shift
        $response = $this->actingAs($this->userB)->get("/earnings/{$entryA->id}");
        $response->assertStatus(403);
    }

    public function test_user_cannot_delete_another_users_earnings(): void
    {
        $entryA = EarningsEntry::create([
            'user_id' => $this->userA->id,
            'work_date' => date('Y-m-d'),
            'gross_earnings' => 500.00,
            'incentives' => 0.00,
            'platform_deductions' => 0.00,
            'fuel_expenses' => 100.00,
            'other_expenses' => 0.00,
            'hours_worked' => 4.0,
            'total_income' => 500.00,
            'total_expenses' => 100.00,
            'net_earnings' => 400.00,
            'effective_hourly_earnings' => 100.00,
        ]);

        $response = $this->actingAs($this->userB)->delete("/earnings/{$entryA->id}");
        $response->assertStatus(403);

        $this->assertDatabaseHas('earnings_entries', ['id' => $entryA->id]);
    }

    public function test_user_cannot_update_or_delete_another_users_complaint(): void
    {
        $complaintA = Complaint::create([
            'user_id' => $this->userA->id,
            'company_name' => 'Swiggy',
            'issue_category' => 'Incentive Shortfall',
            'expected_amount' => 500.00,
            'received_amount' => 300.00,
            'description' => 'Target not credited despite 22 completed orders.',
            'preferred_language' => 'English',
            'status' => 'draft',
        ]);

        // Try to update
        $updateResponse = $this->actingAs($this->userB)->put("/complaints/{$complaintA->id}", [
            'company_name' => 'Hacked Company',
            'issue_category' => 'Incentive Shortfall',
            'description' => 'Malicious update attempt',
            'preferred_language' => 'English',
            'status' => 'cancelled',
        ]);
        $updateResponse->assertStatus(403);

        // Try to delete
        $deleteResponse = $this->actingAs($this->userB)->delete("/complaints/{$complaintA->id}");
        $deleteResponse->assertStatus(403);

        $this->assertDatabaseHas('complaints', [
            'id' => $complaintA->id,
            'company_name' => 'Swiggy',
        ]);
    }

    public function test_user_cannot_post_messages_to_another_users_conversation(): void
    {
        $convA = $this->userA->assistantConversations()->create(['title' => 'Private Advice']);

        $response = $this->actingAs($this->userB)->postJson("/assistant/{$convA->id}/message", [
            'message' => 'Unauthorized injection',
            'language' => 'English',
        ]);

        $response->assertStatus(403);
    }

    public function test_form_validation_rejects_invalid_inputs(): void
    {
        // 1. Invalid email registration
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'not-an-email',
            'password' => '123', // too short
            'password_confirmation' => '456', // mismatch
        ]);
        $response->assertSessionHasErrors(['email', 'password']);

        // 2. Negative earnings amounts
        $earningsResponse = $this->actingAs($this->userA)->post('/earnings', [
            'work_date' => 'not-a-date',
            'gross_earnings' => -500.00,
            'hours_worked' => -2,
        ]);
        $earningsResponse->assertSessionHasErrors(['work_date', 'gross_earnings', 'hours_worked']);
    }
}
