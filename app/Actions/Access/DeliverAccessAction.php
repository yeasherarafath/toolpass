<?php

namespace App\Actions\Access;

use App\Events\Access\AccessDelivered;
use App\Models\UserToolAccess;

class DeliverAccessAction
{
    public function handle(UserToolAccess $access): UserToolAccess
    {
        $method = $access->order?->package?->delivery_type ?? 'invite';

        if ($method === 'invite') {
            $access->delivery_status = 'delivered';
            $access->invited_at = now();
            $access->status = 'pending';
        } else {
            $access->delivery_status = 'delivered';
            $access->delivered_at = now();
            $access->status = 'active';
        }

        $access->save();

        event(new AccessDelivered($access));

        return $access;
    }
}
