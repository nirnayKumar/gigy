@extends('layouts.app')

@section('title', 'Worker AI Assistant')

@section('content')
<div style="max-width: 1000px; margin: 0 auto; height: calc(100vh - 160px); display: flex; flex-direction: column;">
    <!-- Chat Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.5rem;">
        <div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div class="brand-badge" style="width: 32px; height: 32px; font-size: 1rem;">G</div>
                <h1 style="font-size: 1.45rem; font-weight: 800; color: var(--gray-900);">Worker Assistant</h1>
                <span class="badge badge-success">Gemini 3.8 Flash</span>
            </div>
            <p style="font-size: 0.8rem; color: var(--gray-600); margin-top: 0.15rem;">
                Practical guidance on payouts, deductions, welfare schemes (e-Shram), road safety, and grievance procedures.
            </p>
        </div>

        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <form method="POST" action="{{ route('assistant.store') }}" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-secondary btn-sm">
                    + New Conversation
                </button>
            </form>

            @if($activeConversation)
                <form method="POST" action="{{ route('assistant.destroy', $activeConversation) }}" onsubmit="return confirm('Delete this conversation?');" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-secondary btn-sm" style="color: var(--danger); border-color: #fecaca;" title="Delete Conversation">
                        Delete
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Chat Container -->
    <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; flex: 1; min-height: 0; @media(min-width: 768px){grid-template-columns: 240px 1fr;}">
        <!-- Conversations Sidebar (Desktop) -->
        <div class="card" style="margin-bottom: 0; padding: 0.75rem; display: none; overflow-y: auto; @media(min-width: 768px){display: flex; flex-direction: column;}">
            <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; color: var(--gray-600); margin-bottom: 0.5rem; padding: 0 0.25rem;">
                Chat History
            </div>
            <div style="display: flex; flex-direction: column; gap: 0.35rem; flex: 1;">
                @foreach($conversations as $conv)
                    <a href="{{ route('assistant.show', $conv) }}" style="text-decoration: none; padding: 0.6rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; color: {{ $activeConversation && $activeConversation->id === $conv->id ? '#065f46' : 'var(--gray-700)' }}; background: {{ $activeConversation && $activeConversation->id === $conv->id ? '#ecfdf5' : 'transparent' }}; border: 1px solid {{ $activeConversation && $activeConversation->id === $conv->id ? '#a7f3d0' : 'transparent' }}; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; display: block;">
                        💬 {{ $conv->title ?: 'Conversation #' . $conv->id }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Main Chat Area -->
        <div class="card" style="margin-bottom: 0; padding: 0; display: flex; flex-direction: column; overflow: hidden; border: 1px solid var(--gray-300);">
            <!-- Active Conversation Bar & Language Selector -->
            <div style="padding: 0.75rem 1rem; background: var(--gray-50); border-bottom: 1px solid var(--gray-200); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                <div style="font-size: 0.875rem; font-weight: 700; color: var(--gray-800);">
                    {{ $activeConversation->title ?? 'New Conversation' }}
                </div>

                <div style="display: flex; align-items: center; gap: 0.4rem;">
                    <label for="chatLanguage" style="font-size: 0.75rem; font-weight: 600; color: var(--gray-600);">Language:</label>
                    <select id="chatLanguage" class="form-control" style="padding: 0.25rem 0.5rem; font-size: 0.8rem; width: auto;">
                        <option value="English" {{ Auth::user()->preferred_language === 'English' ? 'selected' : '' }}>English</option>
                        <option value="Hindi" {{ Auth::user()->preferred_language === 'Hindi' ? 'selected' : '' }}>Hindi (हिंदी)</option>
                        <option value="Punjabi" {{ Auth::user()->preferred_language === 'Punjabi' ? 'selected' : '' }}>Punjabi (ਪੰਜਾਬੀ)</option>
                    </select>
                </div>
            </div>

            <!-- Messages Stream -->
            <div id="messagesContainer" style="flex: 1; overflow-y: auto; padding: 1.25rem; display: flex; flex-direction: column; gap: 1rem; background: #ffffff;">
                @if($activeConversation && $activeConversation->messages)
                    @foreach($activeConversation->messages as $msg)
                        <div style="display: flex; gap: 0.6rem; align-self: {{ $msg->role === 'user' ? 'flex-end' : 'flex-start' }}; max-width: 85%;">
                            @if($msg->role !== 'user')
                                <div style="width: 28px; height: 28px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">
                                    G
                                </div>
                            @endif
                            <div style="background: {{ $msg->role === 'user' ? '#0f172a' : '#f8fafc' }}; color: {{ $msg->role === 'user' ? '#ffffff' : '#1e293b' }}; border: 1px solid {{ $msg->role === 'user' ? '#0f172a' : '#e2e8f0' }}; border-radius: var(--radius-md); padding: 0.85rem 1rem; font-size: 0.925rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">
                                {{ $msg->content }}
                            </div>
                        </div>
                    @endforeach
                @endif
                <div id="typingIndicator" style="display: none; align-self: flex-start; max-width: 80%;">
                    <div style="display: flex; gap: 0.6rem;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">
                            G
                        </div>
                        <div style="background: #f1f5f9; border-radius: var(--radius-md); padding: 0.75rem 1rem; font-size: 0.85rem; color: var(--gray-600); font-style: italic;">
                            Gigy is thinking...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Suggestions Chips -->
            <div style="padding: 0.5rem 1rem; background: var(--gray-50); border-top: 1px solid var(--gray-200); display: flex; gap: 0.5rem; overflow-x: auto; white-space: nowrap;">
                <button type="button" class="suggestion-chip" onclick="fillPrompt('How does e-Shram card and PM SVANidhi benefit gig workers in India?')" style="background: #ffffff; border: 1px solid var(--gray-300); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: var(--gray-700); cursor: pointer;">
                    📜 e-Shram welfare schemes
                </button>
                <button type="button" class="suggestion-chip" onclick="fillPrompt('Why was my peak hour target incentive deducted and how do I dispute it?')" style="background: #ffffff; border: 1px solid var(--gray-300); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: var(--gray-700); cursor: pointer;">
                    💰 Disputing missing incentives
                </button>
                <button type="button" class="suggestion-chip" onclick="fillPrompt('What are key safety practices and heatwave hydration tips for two-wheeler delivery riders?')" style="background: #ffffff; border: 1px solid var(--gray-300); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: var(--gray-700); cursor: pointer;">
                    🛵 Road safety & heat advice
                </button>
                <button type="button" class="suggestion-chip" onclick="fillPrompt('How to file a grievance against unfair cancellation charges?')" style="background: #ffffff; border: 1px solid var(--gray-300); padding: 0.25rem 0.65rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; color: var(--gray-700); cursor: pointer;">
                    ⚖️ Grievance process
                </button>
            </div>

            <!-- Message Input Form -->
            <div style="padding: 0.75rem 1rem; background: #ffffff; border-top: 1px solid var(--gray-200);">
                <form id="chatForm" method="POST" action="{{ route('assistant.message', $activeConversation) }}" style="display: flex; gap: 0.5rem; align-items: center;">
                    @csrf
                    <input type="hidden" name="language" id="formLanguage" value="{{ Auth::user()->preferred_language }}">
                    <input id="messageInput" type="text" name="message" class="form-control" placeholder="Ask about incentives, deductions, e-Shram, or grievance procedures..." autocomplete="off" required style="font-size: 0.95rem;">
                    <button type="submit" id="btnSend" class="btn btn-primary" style="padding: 0.65rem 1.25rem; flex-shrink: 0;">
                        <span>Send</span>
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const messagesContainer = document.getElementById('messagesContainer');
    const messageInput = document.getElementById('messageInput');
    const chatForm = document.getElementById('chatForm');
    const btnSend = document.getElementById('btnSend');
    const typingIndicator = document.getElementById('typingIndicator');
    const chatLanguage = document.getElementById('chatLanguage');
    const formLanguage = document.getElementById('formLanguage');

    chatLanguage.addEventListener('change', function () {
        formLanguage.value = this.value;
    });

    function scrollToBottom() {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    scrollToBottom();

    function fillPrompt(text) {
        messageInput.value = text;
        messageInput.focus();
    }

    chatForm.addEventListener('submit', async function (e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        const lang = chatLanguage.value;
        formLanguage.value = lang;

        // Append user bubble to UI
        const userDiv = document.createElement('div');
        userDiv.style.display = 'flex';
        userDiv.style.alignSelf = 'flex-end';
        userDiv.style.maxWidth = '85%';
        userDiv.innerHTML = `
            <div style="background: #0f172a; color: #ffffff; border: 1px solid #0f172a; border-radius: var(--radius-md); padding: 0.85rem 1rem; font-size: 0.925rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">
                ${escapeHtml(text)}
            </div>
        `;
        messagesContainer.appendChild(userDiv);

        messageInput.value = '';
        messageInput.disabled = true;
        btnSend.disabled = true;
        typingIndicator.style.display = 'block';
        messagesContainer.appendChild(typingIndicator);
        scrollToBottom();

        try {
            const response = await fetch("{{ route('assistant.message', $activeConversation) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message: text,
                    language: lang
                })
            });

            const data = await response.json();
            typingIndicator.style.display = 'none';

            if (data.success && data.assistant_message) {
                const aiDiv = document.createElement('div');
                aiDiv.style.display = 'flex';
                aiDiv.style.gap = '0.6rem';
                aiDiv.style.alignSelf = 'flex-start';
                aiDiv.style.maxWidth = '85%';
                aiDiv.innerHTML = `
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #059669; color: white; display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 800; flex-shrink: 0;">
                        G
                    </div>
                    <div style="background: #f8fafc; color: #1e293b; border: 1px solid #e2e8f0; border-radius: var(--radius-md); padding: 0.85rem 1rem; font-size: 0.925rem; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">
                        ${escapeHtml(data.assistant_message.content)}
                    </div>
                `;
                messagesContainer.appendChild(aiDiv);
            } else {
                alert('Could not get response: ' + (data.message || 'Unknown error'));
            }
        } catch (err) {
            console.error(err);
            typingIndicator.style.display = 'none';
            alert('Failed to connect to AI assistant.');
        } finally {
            messageInput.disabled = false;
            btnSend.disabled = false;
            messageInput.focus();
            scrollToBottom();
        }
    });

    function escapeHtml(str) {
        return str
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endpush
