<?php

namespace App\Actions\Otp;

use App\Actions\Otp\HandlesOtpRateLimit;
use App\Models\OtpRequest;
use App\Models\User;

class RequestOtpAction
{
    use HandlesOtpRateLimit;

    public function handle(User $user, array $data): OtpRequest
    {
        $this->assertWithinRateLimit($user->id);

        return OtpRequest::create([
            'user_id' => $user->id,
            'order_id' => $data['order_id'] ?? null,
            'user_tool_access_id' => $data['user_tool_access_id'] ?? null,
            'tool_id' => $data['tool_id'] ?? null,
            'tool_account_id' => $data['tool_account_id'] ?? null,
            'request_type' => $data['request_type'] ?? 'otp',
            'status' => 'pending',
            'customer_message' => $data['customer_message'] ?? null,
        ]);
    }
}
