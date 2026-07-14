<?php

namespace App\Actions\Device;

use App\Models\DeviceResetRequest;
use App\Models\UserToolDevice;

class ResetDeviceAction
{
    public function handle(DeviceResetRequest $request, int $adminId): DeviceResetRequest
    {
        if ($request->status === 'completed') {
            return $request;
        }

        if ($request->old_device_id) {
            $device = UserToolDevice::find($request->old_device_id);
            if ($device && $device->status !== 'removed') {
                $device->status = 'removed';
                $device->removed_by = $adminId;
                $device->removed_at = now();
                $device->save();

                $account = $device->toolAccount;
                if ($account && $account->used_devices > 0) {
                    $account->decrement('used_devices');
                }
            }
        }

        $request->status = 'completed';
        $request->reviewed_by = $adminId;
        $request->reviewed_at = now();
        $request->completed_at = now();
        $request->save();

        return $request;
    }
}
