<?php

namespace App\Actions\Orders;

use App\Actions\Access\CreateOrderAccessesAction;
use App\Actions\Concerns\ResolvesOrderReadiness;
use App\Models\Order;

class MarkOrderReadyAction
{
    use ResolvesOrderReadiness;

    public function handle(Order $order): Order
    {
        if (! $this->isReady($order)) {
            return $order;
        }

        if (in_array($order->order_status, ['ready', 'completed', 'expired', 'cancelled'], true)) {
            return $order;
        }

        $order->order_status = 'ready';
        $order->save();

        app(CreateOrderAccessesAction::class)->handle($order);

        return $order;
    }
}
