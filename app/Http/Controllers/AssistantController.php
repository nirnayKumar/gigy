<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendMessageRequest;
use App\Models\AssistantConversation;
use App\Services\GeminiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AssistantController extends Controller
{
    public function __construct(
        protected GeminiService $geminiService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();
        $conversations = $user->assistantConversations()->withCount('messages')->get();

        // Get active conversation or create default welcome conversation
        $activeConversation = $conversations->first();
        if (!$activeConversation) {
            $activeConversation = DB::transaction(function () use ($user) {
                $conv = $user->assistantConversations()->create([
                    'title' => 'General Assistance',
                ]);
                $conv->messages()->create([
                    'role' => 'assistant',
                    'content' => "Namaste! I am Gigy, your AI companion. I'm here to help you understand your earnings, platform deductions, dispute procedures, road safety, vehicle expenses, and Indian social security schemes like e-Shram. How can I help you today?",
                ]);
                return $conv;
            });

            $conversations = $user->assistantConversations()->withCount('messages')->get();
        }

        $activeConversation->load('messages');

        return view('assistant.index', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
        ]);
    }

    public function show(Request $request, AssistantConversation $conversation): View
    {
        $this->authorize('view', $conversation);

        $conversations = $request->user()->assistantConversations()->withCount('messages')->get();
        $conversation->load('messages');

        return view('assistant.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
        ]);

        $conversation = DB::transaction(function () use ($request, $validated) {
            $conv = $request->user()->assistantConversations()->create([
                'title' => $validated['title'] ?? 'New Conversation',
            ]);

            $conv->messages()->create([
                'role' => 'assistant',
                'content' => "Hello! How can I assist you in this conversation? Feel free to ask in English, Hindi, or Punjabi.",
            ]);

            return $conv;
        });

        return redirect()->route('assistant.show', $conversation);
    }

    public function sendMessage(SendMessageRequest $request, AssistantConversation $conversation): JsonResponse|RedirectResponse
    {
        $this->authorize('update', $conversation);

        $validated = $request->validated();
        $userMessageText = trim($validated['message']);
        $language = $validated['language'] ?? ($request->user()->preferred_language ?? 'English');

        // Store user message in transaction
        $userMsg = DB::transaction(function () use ($conversation, $userMessageText) {
            return $conversation->messages()->create([
                'role' => 'user',
                'content' => $userMessageText,
            ]);
        });

        // Auto-update conversation title if default
        if (in_array($conversation->title, ['General Assistance', 'New Conversation', null])) {
            $shortTitle = mb_substr($userMessageText, 0, 35) . (mb_strlen($userMessageText) > 35 ? '...' : '');
            $conversation->update(['title' => $shortTitle]);
        }

        // Call Gemini AI
        $aiReply = $this->geminiService->assistantChat($conversation, $userMessageText, $language);

        // Store assistant response in transaction
        $assistantMsg = DB::transaction(function () use ($conversation, $aiReply) {
            return $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $aiReply,
            ]);
        });

        $conversation->touch();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'user_message' => $userMsg,
                'assistant_message' => $assistantMsg,
            ]);
        }

        return redirect()->route('assistant.show', $conversation);
    }

    public function destroy(Request $request, AssistantConversation $conversation): RedirectResponse
    {
        $this->authorize('delete', $conversation);

        DB::transaction(function () use ($conversation) {
            $conversation->delete();
        });

        return redirect()->route('assistant.index')->with('success', 'Conversation deleted.');
    }
}
