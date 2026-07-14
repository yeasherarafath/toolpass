<?php

namespace App\Actions\Payments;

use App\Actions\Concerns\GeneratesReference;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;

class CreatePaymentAction
{
    use GeneratesReference;

    public function handle(Order $order, User $user, array $data): Payment
    {
        return Payment::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'amount' => $data['amount'] ?? $order->payable_amount,
            'currency' => $order->currency,
            'method' => $data['method'] ?? 'bkash',
            'transaction_id' => $this->generatePaymentReference(),
            'sender_number' => $data['sender_number'] ?? null,
            'screenshot' => $data['screenshot'] ?? null,
            'status' => 'pending',
            'note' => $data['note'] ?? null,
        ]);
    }
}
