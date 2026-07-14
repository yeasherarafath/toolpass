<?php

namespace App\Listeners;

use App\Events\Payment\PaymentVerified;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPaymentVerified implements ShouldQueue
{
    public function handle(PaymentVerified $event): void
    {
        $payment = $event->payment;

        Notification::create([
            'user_id' => $payment->user_id,
            'channel' => 'email',
            'type' => 'payment',
            'subject' => 'Payment verified',
            'body' => 'Your payment of ' . $payment->amount . ' ' . $payment->currency . ' has been verified. Your order is now being processed.',
            'status' => 'pending',
        ]);
    }
}
