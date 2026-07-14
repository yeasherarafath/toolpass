<?php

namespace App\Actions\Otp;

use Illuminate\Support\Facades\Cache;

trait HandlesOtpRateLimit
{
    protected function assertWithinRateLimit(int $userId, int $maxPerMinute = 3): void
    {
        $key = 'otp_rate_' . $userId;
        $count = (int) Cache::get($key, 0);

        if ($count >= $maxPerMinute) {
            throw new \RuntimeException('Too many OTP requests. Please wait before requesting again.');
        }

        Cache::put($key, $count + 1, now()->addMinute());
    }

    protected function clearRateLimit(int $userId): void
    {
        Cache::forget('otp_rate_' . $userId);
    }
}
