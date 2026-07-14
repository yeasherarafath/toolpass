<?php

namespace App\Listeners;

use App\Events\Order\OrderCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyOrderPlaced implements ShouldQueue
{
    public function handle(OrderCreated $event): void
    {
        $order = $event->order;

        Notification::create([
            'user_id' => $order->user_id,
            'channel' => 'email',
            'type' => 'order',
            'subject' => 'Order placed: ' . $order->order_number,
            'body' => 'Your order for "' . ($order->package?->name ?? 'package') . '" has been placed. We will review your payment shortly.',
            'status' => 'pending',
        ]);
    }
}
