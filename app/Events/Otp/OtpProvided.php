<?php

namespace App\Events\Otp;

use App\Models\OtpRequest;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpProvided
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public OtpRequest $otpRequest)
    {
    }

    public function activityDescription(): string
    {
        return 'OTP provided for ' . ($this->otpRequest->tool?->name ?? 'tool');
    }

    public function activitySubject(): OtpRequest
    {
        return $this->otpRequest;
    }
}
