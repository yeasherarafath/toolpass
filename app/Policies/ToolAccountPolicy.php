<?php

namespace App\Policies;

use App\Models\ToolAccount;
use App\Models\User;

class ToolAccountPolicy
{
    public function view(User $user, ToolAccount $account): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function viewCredentials(User $user, ToolAccount $account): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }

    public function manage(User $user, ToolAccount $account): bool
    {
        return in_array($user->role, ['admin', 'staff']);
    }
}
