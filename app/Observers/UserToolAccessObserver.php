<?php

namespace App\Observers;

use App\Actions\Concerns\SyncsAccountSlots;
use App\Events\Access\AccessExpired;
use App\Models\UserToolAccess;

class UserToolAccessObserver
{
    public function created(UserToolAccess $access): void
    {
        SyncsAccountSlots::assignAndIncrement($access);
    }

    public function updated(UserToolAccess $access): void
    {
        if ($access->wasChanged('status') && in_array($access->status, ['expired', 'revoked'], true)) {
            SyncsAccountSlots::decrement($access);
            event(new AccessExpired($access));
        }
    }

    public function deleted(UserToolAccess $access): void
    {
        SyncsAccountSlots::decrement($access);
        event(new AccessExpired($access));
    }
}
