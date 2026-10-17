<?php

namespace App\Http\Controllers;

use App\Models\AssistantConversation;
use App\Models\AssistantMessage;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssistantController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $conversations = $user->assistantConversations()->withCount('messages')->get();

        // Get latest conversation or create one
        $activeConversation = $conversations->first();
        if (!$activeConversation) {
            $activeConversation = $user->assistantConversations()->create([
                'title' => 'General Assistance',
            ]);
            // Add a warm welcome message from Gigy
            $activeConversation->messages()->create([
                'role' => 'assistant',
                'content' => "Namaste! I am Gigy, your AI companion. I'm here to help you understand your earnings, platform deductions, dispute procedures, road safety, vehicle expenses, and Indian social security schemes like e-Shram. How can I help you today?",
            ]);
            $conversations = $user->assistantConversations()->withCount('messages')->get();
        }

        $activeConversation->load('messages');

        return view('assistant.index', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
        ]);
    }

    public function show(Request $request, AssistantConversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $conversations = $request->user()->assistantConversations()->withCount('messages')->get();
        $conversation->load('messages');

        return view('assistant.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:100'],
        ]);

        $conversation = $request->user()->assistantConversations()->create([
            'title' => $validated['title'] ?? 'New Conversation',
        ]);

        // Add welcome message
        $conversation->messages()->create([
            'role' => 'assistant',
            'content' => "Hello! How can I assist you in this conversation? Feel free to ask in English, Hindi, or Punjabi.",
        ]);

        return redirect()->route('assistant.show', $conversation);
    }

    public function sendMessage(Request $request, AssistantConversation $conversation, GeminiService $gemini)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'language' => ['nullable', 'string', 'in:English,Hindi,Punjabi'],
        ]);

        $userMessageText = trim($validated['message']);
        $language = $validated['language'] ?? ($request->user()->preferred_language ?? 'English');

        // Store user message
        $userMsg = $conversation->messages()->create([
            'role' => 'user',
            'content' => $userMessageText,
        ]);

        // Auto-update title if it's generic
        if (in_array($conversation->title, ['General Assistance', 'New Conversation', null])) {
            $shortTitle = mb_substr($userMessageText, 0, 35) . (mb_strlen($userMessageText) > 35 ? '...' : '');
            $conversation->update(['title' => $shortTitle]);
        }

        // Call Gemini for AI response
        $aiReply = $gemini->assistantChat($conversation, $userMessageText, $language);

        // Store assistant message
        $assistantMsg = $conversation->messages()->create([
            'role' => 'assistant',
            'content' => $aiReply,
        ]);

        $conversation->touch(); // updates updated_at

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'user_message' => $userMsg,
                'assistant_message' => $assistantMsg,
            ]);
        }

        return redirect()->route('assistant.show', $conversation);
    }

    public function destroy(AssistantConversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this conversation.');
        }

        $conversation->delete();

        return redirect()->route('assistant.index')->with('success', 'Conversation deleted.');
    }
}
