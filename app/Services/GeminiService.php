<?php

namespace App\Services;

use App\Models\AssistantConversation;
use App\Models\EarningsEntry;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected ?string $apiKey;
    protected string $model;
    protected int $timeout = 25;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
        $this->model = config('services.gemini.model', 'gemini-2.5-flash');
    }

    /**
     * Check if Gemini API key is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Query Gemini API to list models or check active models.
     */
    public function getAvailableModels(): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        try {
            $response = Http::timeout(10)->get('https://generativelanguage.googleapis.com/v1beta/models', [
                'key' => $this->apiKey,
            ]);

            if ($response->successful()) {
                $models = $response->json('models', []);
                return array_map(function ($m) {
                    return str_replace('models/', '', $m['name'] ?? '');
                }, $models);
            }
        } catch (\Throwable $e) {
            Log::warning('Gemini getAvailableModels failed: ' . $e->getMessage());
        }

        return [];
    }

    public function getActiveModel(): string
    {
        // Verified supported models on Google API: gemini-2.5-flash, gemini-flash-latest, etc.
        $supportedModels = [
            'gemini-2.5-flash',
            'gemini-flash-latest',
            'gemini-2.5-pro',
            'gemini-flash-lite-latest',
        ];

        if (in_array($this->model, $supportedModels)) {
            return $this->model;
        }

        // Default to verified fast flash model
        return 'gemini-2.5-flash';
    }

    /**
     * Generate content via Gemini API with verified models.
     */
    public function generate(string $prompt, ?string $systemInstruction = null): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'content' => 'Gemini API key is not configured. Please set GEMINI_API_KEY in your .env file.',
            ];
        }

        $activeModel = $this->getActiveModel();
        $modelsToTry = array_unique([$activeModel, 'gemini-2.5-flash', 'gemini-flash-latest']);

        $lastError = 'Unknown error';

        foreach ($modelsToTry as $modelName) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key=" . urlencode($this->apiKey);

                $payload = [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature' => 0.4,
                        'maxOutputTokens' => 1500,
                    ],
                ];

                if ($systemInstruction) {
                    $payload['systemInstruction'] = [
                        'parts' => [
                            ['text' => $systemInstruction],
                        ],
                    ];
                }

                $response = Http::timeout($this->timeout)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post($url, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($text) {
                        return [
                            'success' => true,
                            'model_used' => $modelName,
                            'content' => trim($text),
                        ];
                    }
                }

                $errorData = $response->json();
                $errorMessage = $errorData['error']['message'] ?? ('HTTP ' . $response->status());
                $lastError = "Model {$modelName}: " . $errorMessage;
                Log::warning("Gemini attempt with {$modelName} failed: " . $errorMessage);

                // If it's a quota or rate-limit or 404, we try next model
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::error("Gemini exception with {$modelName}: " . $e->getMessage());
            }
        }

        return [
            'success' => false,
            'content' => "AI Service Error: {$lastError}. Please check your Gemini API key and quota.",
        ];
    }

    /**
     * Feature A: Explain My Earnings with AI
     */
    public function explainEarnings(EarningsEntry $entry, string $language = 'English'): string
    {
        $system = "You are Gigy, an expert AI companion advocating for gig economy workers (delivery partners, cab drivers, quick-commerce couriers) in India.
Provide clear, empathetic, and highly practical explanations of daily earnings, incentives, and deductions.
Languages supported: English, Hindi (written in readable script or easy Hindi), and Punjabi.
Respond strictly in the requested language: {$language}.
Guidelines:
1. Explain total income (gross + incentives), total expenses, net earnings, and effective hourly earnings.
2. Identify any unusual deductions or low hourly returns.
3. Distinguish facts from assumptions.
4. Do NOT invent amounts, do NOT declare deductions illegal without clear evidence, and do NOT falsely claim to have contacted a platform.
5. Provide 2-3 realistic next steps (e.g. tracking surge hours, requesting deduction breakdowns, comparing trip logs).
Keep the tone supportive, direct, and easy to read on a mobile phone.";

        $prompt = "Please explain the following gig shift earnings for a worker in India:
- Date: {$entry->work_date->format('d M Y')}
- Gross Earnings: ₹" . number_format($entry->gross_earnings, 2) . "
- Incentives: ₹" . number_format($entry->incentives, 2) . "
- Fuel Expenses: ₹" . number_format($entry->fuel_expenses, 2) . "
- Other Expenses: ₹" . number_format($entry->other_expenses, 2) . "
- Platform Deductions: ₹" . number_format($entry->platform_deductions, 2) . "
- Total Income: ₹" . number_format($entry->total_income, 2) . "
- Total Expenses: ₹" . number_format($entry->total_expenses, 2) . "
- Net Take-Home Earnings: ₹" . number_format($entry->net_earnings, 2) . "
- Hours Worked: {$entry->hours_worked} hours
- Effective Hourly Earnings: ₹" . number_format($entry->effective_hourly_earnings, 2) . "/hr
- Worker Shift Notes: " . ($entry->notes ?: 'None provided') . "

Language requested: {$language}. Give a concise, structured breakdown with actionable advice.";

        $result = $this->generate($prompt, $system);
        return $result['content'];
    }

    /**
     * Feature B: Worker Assistant Chat
     */
    public function assistantChat(AssistantConversation $conversation, string $userMessage, string $language = 'English'): string
    {
        $system = "You are Gigy, an AI Companion for gig workers in India (Zomato, Swiggy, Uber, Ola, Zepto, Blinkit, Porter, Urban Company, Amazon Flex, shadowfax, etc.).
Your job is to provide practical, reliable guidance on:
- Earnings disputes and incentive qualification
- Fuel, maintenance, and vehicle expense management
- Platform deduction policies and grievance mechanisms
- Road safety, heatwave/monsoon gear, and rest schedules
- Document requirements (PAN, Aadhaar, driving license, vehicle insurance, RC)
- Social security & government welfare schemes in India (e.g., e-Shram portal, PM SVANidhi, state gig worker boards like Rajasthan/Karnataka Gig Worker Acts)
- How to draft formal dispute tickets to support teams
Guidelines:
- Languages supported: English, Hindi, and Punjabi. Reply in {$language}.
- Never invent laws, schemes, eligibility rules, deadlines, or official helpline numbers.
- Where appropriate, direct workers to verified official resources (e.g., official platform partner app help section, e-Shram portal, labor commissioners).
- Be polite, supportive, concise, and mobile-friendly.";

        // Build brief history context from past messages
        $historyText = "";
        $recentMessages = $conversation->messages()->latest()->take(6)->get()->reverse();
        foreach ($recentMessages as $msg) {
            $sender = $msg->role === 'user' ? 'Worker' : 'Gigy';
            $historyText .= "{$sender}: {$msg->content}\n";
        }

        $prompt = "Conversation History:\n" . ($historyText ?: "No previous messages.\n") . "\nWorker's New Question: {$userMessage}\n\nRespond in {$language}.";

        $result = $this->generate($prompt, $system);
        return $result['content'];
    }

    /**
     * Feature C: Complaint Generator
     */
    public function generateComplaintDraft(array $data, string $language = 'English'): string
    {
        $system = "You are Gigy's Complaint Generator for Indian gig workers and delivery partners.
Your goal is to generate a professional, firm, respectful, and well-structured formal dispute letter or ticket draft that a worker can send to platform support or grievance officers.
Always label this as a DRAFT.
Never claim the complaint has been submitted externally.
Language requested: {$language}.";

        $company = $data['company_name'] ?? 'Platform Support';
        $category = $data['issue_category'] ?? 'Payment Dispute';
        $expected = isset($data['expected_amount']) ? '₹' . number_format($data['expected_amount'], 2) : 'Not specified';
        $received = isset($data['received_amount']) ? '₹' . number_format($data['received_amount'], 2) : 'Not specified';
        $incidentDate = !empty($data['incident_date']) ? $data['incident_date'] : 'Recent shift';
        $description = $data['description'] ?? '';

        $prompt = "Draft a formal grievance / complaint ticket for a gig worker in India with the following details:
- Platform / Company: {$company}
- Issue Category: {$category}
- Incident Date: {$incidentDate}
- Expected Amount: {$expected}
- Received Amount: {$received}
- Description of Issue: {$description}
- Language: {$language}

Format the response clearly with:
1. Subject line
2. Formal salutation
3. Clear factual description of the order / shift and discrepancy (expected vs received)
4. Specific requested resolution (reimbursement / adjustment / audit)
5. Polite closing with placeholder for Partner ID / Contact Number.

Keep it assertive yet respectful.";

        $result = $this->generate($prompt, $system);
        return $result['content'];
    }
}
