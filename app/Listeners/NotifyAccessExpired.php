<?php

namespace App\Listeners;

use App\Events\Access\AccessExpired;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyAccessExpired implements ShouldQueue
{
    public function handle(AccessExpired $event): void
    {
        $access = $event->access;

        Notification::create([
            'user_id' => $access->user_id,
            'channel' => 'email',
            'type' => 'access',
            'subject' => 'Tool access expired: ' . ($access->tool?->name ?? 'Tool'),
            'body' => 'Your access for "' . ($access->tool?->name ?? 'tool') . '" has expired or been revoked.',
            'status' => 'pending',
        ]);
    }
}
