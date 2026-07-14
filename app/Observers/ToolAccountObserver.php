<?php

namespace App\Observers;

use App\Events\ToolAccount\ToolAccountCreated;
use App\Models\ToolAccount;
use Illuminate\Support\Facades\Auth;

class ToolAccountObserver
{
    public function creating(ToolAccount $account): void
    {
        if (Auth::check() && ! $account->created_by) {
            $account->created_by = Auth::id();
        }
    }

    public function updating(ToolAccount $account): void
    {
        if (Auth::check()) {
            $account->updated_by = Auth::id();
        }
    }

    public function created(ToolAccount $account): void
    {
        event(new ToolAccountCreated($account));
    }
}
