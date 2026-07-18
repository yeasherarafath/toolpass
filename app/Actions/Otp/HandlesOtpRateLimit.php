<?php

namespace App\Actions\Otp;

use App\Enum\CacheKeyEnum;
use Illuminate\Support\Facades\RateLimiter;

trait HandlesOtpRateLimit
{
    protected function assertWithinRateLimit(int $userId, int $maxPerMinute = 3): void
    {
        $key = CacheKeyEnum::otpRate($userId);

        if (RateLimiter::tooManyAttempts($key, $maxPerMinute)) {
            throw new \RuntimeException('Too many OTP requests. Please wait before requesting again.');
        }

        RateLimiter::hit($key, 60);
    }

    protected function clearRateLimit(int $userId): void
    {
        RateLimiter::clear(CacheKeyEnum::otpRate($userId));
    }
}
