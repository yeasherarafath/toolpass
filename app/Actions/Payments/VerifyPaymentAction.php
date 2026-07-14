<?php

namespace App\Actions\Payments;

use App\Events\Payment\PaymentVerified;
use App\Models\Payment;
use App\Models\User;

class VerifyPaymentAction
{
    public function handle(Payment $payment, User $admin): Payment
    {
        $payment->status = 'verified';
        $payment->verified_by = $admin->id;
        $payment->verified_at = now();
        $payment->save();

        event(new PaymentVerified($payment));

        return $payment;
    }
}
