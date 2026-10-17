<?php

namespace App\Policies;

use App\Models\EarningsEntry;
use App\Models\User;

class EarningsEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, EarningsEntry $earningsEntry): bool
    {
        return $user->id === $earningsEntry->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, EarningsEntry $earningsEntry): bool
    {
        return $user->id === $earningsEntry->user_id;
    }

    public function delete(User $user, EarningsEntry $earningsEntry): bool
    {
        return $user->id === $earningsEntry->user_id;
    }
}
