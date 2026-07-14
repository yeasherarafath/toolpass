<?php

namespace App\Actions\Concerns;

use App\Models\Order;

trait ResolvesOrderReadiness
{
    public function isReady(Order $order): bool
    {
        return $order->payment_status === 'paid'
            && in_array($order->required_info_status, ['approved', 'not_required'], true);
    }
}
