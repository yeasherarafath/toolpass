<?php

namespace App\Actions\Concerns;

use App\Models\ToolAccount;
use App\Models\UserToolAccess;

class SyncsAccountSlots
{
    public static function assignAndIncrement(UserToolAccess $access): void
    {
        if ($access->tool_account_id) {
            $access->toolAccount?->increment('used_slots');
            return;
        }

        $account = ToolAccount::where('tool_id', $access->tool_id)
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('max_users')->orWhereColumn('used_slots', '<', 'max_users');
            })
            ->orderBy('used_slots')
            ->first();

        if ($account) {
            $access->update(['tool_account_id' => $account->id]);
            $account->increment('used_slots');
        }
    }

    public static function decrement(UserToolAccess $access): void
    {
        if ($access->tool_account_id) {
            $account = $access->toolAccount;
            if ($account && $account->used_slots > 0) {
                $account->decrement('used_slots');
            }
        }
    }
}
