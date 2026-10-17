<?php

namespace Tests\Feature;

use App\Models\AssistantConversation;
use App\Models\Complaint;
use App\Models\EarningsEntry;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GigyWorkflowTest extends TestCase
{
    public function test_gigy_full_end_to_end_workflow(): void
    {
        // 1. Register a new Gigy account
        $testEmail = 'worker_' . uniqid() . '@example.com';
        $registerResponse = $this->post('/register', [
            'name' => 'Rajesh Sharma',
            'email' => $testEmail,
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'worker_type' => 'Food Delivery (Zomato/Swiggy)',
            'preferred_language' => 'English',
        ]);

        $registerResponse->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $user1 = User::where('email', $testEmail)->first();
        $this->assertNotNull($user1);
        $this->assertEquals('Rajesh Sharma', $user1->name);

        // 2. Open dashboard
        $dashboardResponse = $this->actingAs($user1)->get('/dashboard');
        $dashboardResponse->assertStatus(200);

        // 3. Record shift earnings: Gross ₹1,000, Incentives ₹100, Fuel ₹150, Other ₹0, Deductions ₹50, 8 hrs
        $storeEarningsResponse = $this->actingAs($user1)->post('/earnings', [
            'work_date' => date('Y-m-d'),
            'gross_earnings' => 1000.00,
            'incentives' => 100.00,
            'fuel_expenses' => 150.00,
            'other_expenses' => 0.00,
            'platform_deductions' => 50.00,
            'hours_worked' => 8.0,
            'notes' => 'Evening rain shift with long waits',
        ]);

        $entry = EarningsEntry::where('user_id', $user1->id)->latest()->first();
        $this->assertNotNull($entry);
        $storeEarningsResponse->assertRedirect(route('earnings.show', $entry));

        // 4. Verify calculations
        // Total income: 1000 + 100 = 1100
        $this->assertEquals(1100.00, $entry->total_income);
        // Total expenses: 150 + 0 + 50 = 200
        $this->assertEquals(200.00, $entry->total_expenses);
        // Net earnings: 1100 - 200 = 900
        $this->assertEquals(900.00, $entry->net_earnings);
        // Hourly earnings: 900 / 8 = 112.50
        $this->assertEquals(112.50, $entry->effective_hourly_earnings);

        // 5. Test Explain My Earnings with Gemini AI
        $explainResponse = $this->actingAs($user1)->post("/earnings/{$entry->id}/explain", [
            'language' => 'English',
        ]);
        $explainResponse->assertStatus(200);
        $explainResponse->assertSee('Shift Breakdown');

        // 6. Test Worker Assistant AI Chat
        $conv = $user1->assistantConversations()->create(['title' => 'Incentive Question']);
        $chatResponse = $this->actingAs($user1)->postJson("/assistant/{$conv->id}/message", [
            'message' => 'Why do platforms deduct bag deposits and how can I get it refunded?',
            'language' => 'English',
        ]);
        $chatResponse->assertStatus(200);
        $chatResponse->assertJson(['success' => true]);
        $this->assertDatabaseHas('assistant_messages', [
            'conversation_id' => $conv->id,
            'role' => 'user',
        ]);
        $this->assertDatabaseHas('assistant_messages', [
            'conversation_id' => $conv->id,
            'role' => 'assistant',
        ]);

        // 7. Generate a complaint draft and save in MySQL
        $draftResponse = $this->actingAs($user1)->postJson('/complaints/generate-draft', [
            'company_name' => 'Zomato',
            'issue_category' => 'Unfair Platform Deduction',
            'expected_amount' => 1100.00,
            'received_amount' => 900.00,
            'incident_date' => date('Y-m-d'),
            'description' => 'Target bonus of ₹100 was not credited and ₹50 penalty was applied wrongfully.',
            'preferred_language' => 'English',
        ]);
        $draftResponse->assertStatus(200);
        $draftData = $draftResponse->json();
        $this->assertTrue($draftData['success']);
        $this->assertNotEmpty($draftData['draft']);

        $complaintStore = $this->actingAs($user1)->post('/complaints', [
            'company_name' => 'Zomato',
            'issue_category' => 'Unfair Platform Deduction',
            'expected_amount' => 1100.00,
            'received_amount' => 900.00,
            'incident_date' => date('Y-m-d'),
            'description' => 'Target bonus of ₹100 was not credited and ₹50 penalty was applied wrongfully.',
            'preferred_language' => 'English',
            'generated_draft' => $draftData['draft'],
            'status' => 'draft',
            'earnings_entry_id' => $entry->id,
        ]);

        $complaint = Complaint::where('user_id', $user1->id)->latest()->first();
        $this->assertNotNull($complaint);
        $complaintStore->assertRedirect(route('complaints.show', $complaint));

        // 8. Confirm persistence in database
        $this->assertDatabaseHas('earnings_entries', ['id' => $entry->id, 'net_earnings' => 900.00]);
        $this->assertDatabaseHas('complaints', ['id' => $complaint->id, 'company_name' => 'Zomato']);
        $this->assertDatabaseHas('assistant_conversations', ['id' => $conv->id]);

        // 9. Create a second user and verify strict isolation (ownership checks)
        $user2 = User::create([
            'name' => 'Simranjit Kaur',
            'email' => 'worker2_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'worker_type' => 'Ride-hail Cab/Auto',
            'preferred_language' => 'Punjabi',
        ]);

        // User 2 cannot access User 1's earnings entry -> 403
        $forbiddenEarnings = $this->actingAs($user2)->get("/earnings/{$entry->id}");
        $forbiddenEarnings->assertStatus(403);

        // User 2 cannot access User 1's complaint -> 403
        $forbiddenComplaint = $this->actingAs($user2)->get("/complaints/{$complaint->id}");
        $forbiddenComplaint->assertStatus(403);

        // User 2 cannot access User 1's conversation -> 403
        $forbiddenConv = $this->actingAs($user2)->get("/assistant/{$conv->id}");
        $forbiddenConv->assertStatus(403);
    }
}
