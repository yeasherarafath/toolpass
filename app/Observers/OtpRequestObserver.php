<?php

namespace App\Observers;

use App\Actions\AdminTask\CreateAdminTaskAction;
use App\Models\OtpRequest;

class OtpRequestObserver
{
    public function created(OtpRequest $request): void
    {
        if ($request->status !== 'pending') {
            return;
        }

        app(CreateAdminTaskAction::class)->handle([
            'user_id' => $request->user_id,
            'user_tool_access_id' => $request->user_tool_access_id,
            'otp_request_id' => $request->id,
            'type' => 'provide_otp',
            'title' => 'Provide OTP for ' . ($request->tool?->name ?? 'tool'),
            'description' => $request->customer_message,
            'priority' => 'medium',
        ]);
    }
}
