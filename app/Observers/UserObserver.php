<?php

namespace App\Observers;

use App\Events\User\UserCreated;
use App\Models\User;

class UserObserver
{
    public function created(User $user): void
    {
        event(new UserCreated($user));
    }
}
