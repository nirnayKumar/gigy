<?php

namespace Tests\Feature;

use App\Models\AssistantConversation;
use App\Models\EarningsEntry;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GeminiAiReliabilityTest extends TestCase
{
    private User $user;
    private EarningsEntry $entry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->user = User::create([
            'name' => 'Gurpreet Singh',
            'email' => 'gurpreet_' . uniqid() . '@example.com',
            'password' => Hash::make('Password123!'),
            'worker_type' => 'Food Delivery (Zomato/Swiggy)',
            'preferred_language' => 'English',
        ]);

        $this->entry = EarningsEntry::create([
            'user_id' => $this->user->id,
            'work_date' => date('Y-m-d'),
            'gross_earnings' => 1200.00,
            'incentives' => 150.00,
            'platform_deductions' => 40.00,
            'fuel_expenses' => 180.00,
            'other_expenses' => 10.00,
            'hours_worked' => 7.5,
            'total_income' => 1350.00,
            'total_expenses' => 230.00,
            'net_earnings' => 1120.00,
            'effective_hourly_earnings' => 149.33,
        ]);
    }

    public function test_gemini_explains_earnings_with_mocked_success_response(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => '### Shift Breakdown for Today\nYou made a gross of ₹1,200 with an incentive bonus of ₹150. Your net take-home hourly rate is ₹149.33/hr.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->post("/earnings/{$this->entry->id}/explain", [
            'language' => 'English',
        ]);

        $response->assertStatus(200);
        $response->assertSee('Shift Breakdown for Today');
        $response->assertSee('₹149.33/hr');
    }

    public function test_gemini_handles_quota_exhaustion_gracefully(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'error' => [
                    'code' => 429,
                    'message' => 'Resource has been exhausted (e.g. check quota).',
                    'status' => 'RESOURCE_EXHAUSTED',
                ],
            ], 429),
        ]);

        $service = app(GeminiService::class);
        $result = $service->generate('Test prompt');

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('Resource has been exhausted', $result['content']);
        $this->assertStringContainsString('quota', $result['content']);
    }

    public function test_complaint_generator_draft_with_mocked_gemini(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => "Subject: Urgent Dispute Regarding Missing Rain Surge Incentive - ID 98213\n\nDear Partner Support Team,\n\nI am writing to formally dispute an unresolved discrepancy..."],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $response = $this->actingAs($this->user)->postJson('/complaints/generate-draft', [
            'company_name' => 'Swiggy',
            'issue_category' => 'Incentive Shortfall',
            'expected_amount' => 450.00,
            'received_amount' => 200.00,
            'incident_date' => date('Y-m-d'),
            'description' => 'Rain surge incentive was not credited for 5 deliveries completed between 7 PM and 10 PM.',
            'preferred_language' => 'English',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertStringContainsString('Missing Rain Surge Incentive', $data['draft']);
    }

    public function test_assistant_chat_message_flow(): void
    {
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [
                    [
                        'content' => [
                            'parts' => [
                                ['text' => 'Platforms typically refund bag and uniform security deposits within 14 working days after returning items at the local hub.'],
                            ],
                        ],
                    ],
                ],
            ], 200),
        ]);

        $conv = $this->user->assistantConversations()->create(['title' => 'Bag Deposit Refund']);

        $response = $this->actingAs($this->user)->postJson("/assistant/{$conv->id}/message", [
            'message' => 'How can I get my bag deposit back?',
            'language' => 'English',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonPath(
            'assistant_message.content',
            'Platforms typically refund bag and uniform security deposits within 14 working days after returning items at the local hub.'
        );
    }
}
