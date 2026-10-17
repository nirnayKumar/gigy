<?php

namespace App\Policies;

use App\Models\AssistantConversation;
use App\Models\User;

class AssistantConversationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, AssistantConversation $conversation): bool
    {
        return $user->id === $conversation->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, AssistantConversation $conversation): bool
    {
        return $user->id === $conversation->user_id;
    }

    public function delete(User $user, AssistantConversation $conversation): bool
    {
        return $user->id === $conversation->user_id;
    }
}
