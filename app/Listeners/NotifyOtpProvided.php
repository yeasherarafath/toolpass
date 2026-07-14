<?php

namespace App\Listeners;

use App\Events\Otp\OtpProvided;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOtpProvided implements ShouldQueue
{
    public function handle(OtpProvided $event): void
    {
        $request = $event->otpRequest;

        if (! $request->user_id) {
            return;
        }

        Notification::create([
            'user_id' => $request->user_id,
            'channel' => 'email',
            'type' => 'otp',
            'subject' => 'OTP ready for ' . ($request->tool?->name ?? 'tool'),
            'body' => 'Your OTP code is now available. View it in your access panel before it expires.',
            'status' => 'pending',
        ]);
    }
}
