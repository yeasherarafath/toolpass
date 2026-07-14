<?php

namespace App\Observers;

use App\Actions\Orders\MarkOrderReadyAction;
use App\Events\Order\OrderCreated;
use App\Models\Order;

class OrderObserver
{
    public function created(Order $order): void
    {
        event(new OrderCreated($order));
    }

    public function updated(Order $order): void
    {
        if ($order->wasChanged('required_info_status') && $order->required_info_status === 'approved') {
            app(MarkOrderReadyAction::class)->handle($order);
        }

        if ($order->wasChanged('payment_status') && $order->payment_status === 'paid') {
            app(MarkOrderReadyAction::class)->handle($order);
        }
    }
}
