<?php

namespace App\Listeners;

use App\Events\ToolAccount\ToolAccountCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyToolAccountAdded implements ShouldQueue
{
    public function handle(ToolAccountCreated $event): void
    {
        $account = $event->toolAccount;

        if (! $account->created_by) {
            return;
        }

        Notification::create([
            'user_id' => $account->created_by,
            'channel' => 'email',
            'type' => 'announcement',
            'subject' => 'Tool account added: ' . $account->name,
            'body' => 'A new tool account "' . $account->name . '" was created for ' . ($account->tool?->name ?? 'a tool') . '.',
            'status' => 'pending',
        ]);
    }
}
