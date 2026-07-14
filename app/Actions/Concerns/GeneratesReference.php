<?php

namespace App\Actions\Concerns;

use Illuminate\Support\Str;

trait GeneratesReference
{
    protected function generateOrderNumber(): string
    {
        do {
            $ref = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (\App\Models\Order::where('order_number', $ref)->exists());

        return $ref;
    }

    protected function generatePaymentReference(): string
    {
        do {
            $ref = 'PAY-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (\App\Models\Payment::where('transaction_id', $ref)->exists());

        return $ref;
    }
}
