<?php

namespace App\Actions\Device;

use App\Models\UserToolDevice;

class ApproveDeviceAction
{
    public function handle(UserToolDevice $device, int $adminId): UserToolDevice
    {
        if ($device->status === 'active') {
            return $device;
        }

        $device->status = 'active';
        $device->approved_by = $adminId;
        $device->approved_at = now();
        $device->save();

        $account = $device->toolAccount;
        if ($account && $account->used_devices < ($account->max_devices ?? 0)) {
            $account->increment('used_devices');
        }

        return $device;
    }
}
