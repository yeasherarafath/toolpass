<?php

namespace App\Observers;

use App\Models\Payment;

class PaymentObserver
{
    public function updated(Payment $payment): void
    {
        if (! $payment->order) {
            return;
        }

        if ($payment->wasChanged('status') && $payment->status === 'verified') {
            if ($payment->order->payment_status !== 'paid') {
                $payment->order->update(['payment_status' => 'paid']);
            }
        }

        if ($payment->wasChanged('status') && $payment->status === 'rejected') {
            if ($payment->order->payment_status === 'paid') {
                $payment->order->update(['payment_status' => 'pending']);
            }
        }
    }
}
