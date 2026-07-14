<?php

namespace App\Actions\Device;

use App\Models\UserToolAccess;
use Illuminate\Support\Carbon;

trait HandlesDevicePolicy
{
    protected function canRequestReset(UserToolAccess $access): bool
    {
        $account = $access->toolAccount;

        if (! $account) {
            return false;
        }

        if (! $account->allow_device_reset) {
            return false;
        }

        if ($account->device_reset_interval_days > 0) {
            $last = $account->userToolDevices()
                ->whereNotNull('removed_at')
                ->orderByDesc('removed_at')
                ->first();

            if ($last && $last->removed_at->gt(Carbon::now()->subDays($account->device_reset_interval_days))) {
                return false;
            }
        }

        return true;
    }
}
