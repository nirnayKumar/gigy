<?php

namespace App\Policies;

use App\Models\Complaint;
use App\Models\User;

class ComplaintPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Complaint $complaint): bool
    {
        return $user->id === $complaint->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Complaint $complaint): bool
    {
        return $user->id === $complaint->user_id;
    }

    public function delete(User $user, Complaint $complaint): bool
    {
        return $user->id === $complaint->user_id;
    }
}
