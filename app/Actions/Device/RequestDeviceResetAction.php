<?php

namespace App\Actions\Device;

use App\Models\DeviceResetRequest;
use App\Models\UserToolAccess;
use App\Models\UserToolDevice;
use App\Models\User;

class RequestDeviceResetAction
{
    use HandlesDevicePolicy;

    public function handle(UserToolAccess $access, User $user, ?UserToolDevice $device = null, ?string $reason = null): DeviceResetRequest
    {
        if (! $this->canRequestReset($access)) {
            throw new \RuntimeException('Device reset is not allowed for this access.');
        }

        $device = $device ?? $access->userToolDevices()->latest()->first();

        return DeviceResetRequest::create([
            'user_id' => $user->id,
            'user_tool_access_id' => $access->id,
            'tool_id' => $access->tool_id,
            'tool_account_id' => $access->tool_account_id,
            'old_device_id' => $device?->id,
            'status' => 'pending',
            'customer_reason' => $reason,
        ]);
    }
}
