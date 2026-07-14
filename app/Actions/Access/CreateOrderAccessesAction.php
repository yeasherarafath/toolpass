<?php

namespace App\Actions\Access;

use App\Models\Order;
use App\Models\UserToolAccess;

class CreateOrderAccessesAction
{
    public function handle(Order $order): void
    {
        if ($order->order_status !== 'ready') {
            return;
        }

        foreach ($order->package->tools as $tool) {
            $exists = UserToolAccess::where('order_id', $order->id)
                ->where('tool_id', $tool->id)
                ->exists();

            if ($exists) {
                continue;
            }

            UserToolAccess::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'tool_id' => $tool->id,
                'source' => 'purchase',
                'status' => 'pending',
                'delivery_status' => 'pending',
                'starts_at' => now(),
                'expires_at' => now()->addDays($order->package->duration_days ?? 30),
                'created_by' => $order->created_by,
                'updated_by' => $order->created_by,
            ]);
        }
    }
}
