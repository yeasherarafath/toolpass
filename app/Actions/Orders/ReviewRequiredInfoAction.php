<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\User;

class ReviewRequiredInfoAction
{
    public function handle(Order $order, string $decision, User $admin, ?string $reason = null): Order
    {
        if ($decision === 'approve') {
            $order->required_info_status = 'approved';
            $order->required_info_reviewed_by = $admin->id;
            $order->required_info_reviewed_at = now();
        } else {
            $order->required_info_status = 'rejected';
            $order->required_info_reject_reason = $reason;
            $order->required_info_reviewed_by = $admin->id;
            $order->required_info_reviewed_at = now();
        }

        $order->save();

        return $order;
    }
}
