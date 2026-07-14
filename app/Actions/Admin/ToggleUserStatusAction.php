<?php

namespace App\Actions\Admin;

use App\Models\User;
use RuntimeException;

class ToggleUserStatusAction
{
    public function __invoke(User $user, ?User $actor): string
    {
        if ($actor && $actor->id === $user->id && $user->role === 'admin') {
            throw new RuntimeException('You cannot change your own admin status.');
        }

        $user->status = $user->status === 'active' ? 'suspended' : 'active';
        $user->save();

        return 'User status updated.';
    }
}
