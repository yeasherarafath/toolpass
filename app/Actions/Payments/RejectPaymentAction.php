<?php

namespace App\Actions\Payments;

use App\Models\Payment;
use App\Models\User;

class RejectPaymentAction
{
    public function handle(Payment $payment, User $admin, ?string $reason = null): Payment
    {
        $payment->status = 'rejected';
        $payment->verified_by = $admin->id;
        $payment->verified_at = now();
        $payment->reject_reason = $reason;
        $payment->save();

        return $payment;
    }
}
