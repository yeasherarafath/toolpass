<?php

namespace App\Listeners;

use App\Events\Access\AccessDelivered;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAccessDelivered implements ShouldQueue
{
    public function handle(AccessDelivered $event): void
    {
        $access = $event->access;

        Notification::create([
            'user_id' => $access->user_id,
            'channel' => 'email',
            'type' => 'access',
            'subject' => 'Tool access delivered: ' . ($access->tool?->name ?? 'Tool'),
            'body' => 'Your access for "' . ($access->tool?->name ?? 'tool') . '" is now ' . ($access->delivery_status === 'delivered' ? 'delivered' : 'ready') . '.',
            'status' => 'pending',
        ]);
    }
}
