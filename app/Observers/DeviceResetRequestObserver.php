<?php

namespace App\Observers;

use App\Actions\AdminTask\CreateAdminTaskAction;
use App\Models\DeviceResetRequest;

class DeviceResetRequestObserver
{
    public function created(DeviceResetRequest $request): void
    {
        if ($request->status !== 'pending') {
            return;
        }

        app(CreateAdminTaskAction::class)->handle([
            'user_id' => $request->user_id,
            'user_tool_access_id' => $request->user_tool_access_id,
            'device_reset_request_id' => $request->id,
            'type' => 'device_reset',
            'title' => 'Device reset for ' . ($request->tool?->name ?? 'tool'),
            'description' => $request->customer_reason,
            'priority' => 'medium',
        ]);
    }
}
