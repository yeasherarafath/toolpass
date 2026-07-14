<?php

namespace App\Listeners;

use App\Events\User\UserCreated;
use App\Models\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendWelcomeNotification implements ShouldQueue
{
    public function handle(UserCreated $event): void
    {
        if ($event->user->role !== 'customer') {
            return;
        }

        Notification::create([
            'user_id' => $event->user->id,
            'channel' => 'email',
            'type' => 'announcement',
            'subject' => 'Welcome to ToolPass',
            'body' => 'Hi ' . $event->user->name . ', welcome to ToolPass! Your account is ready.',
            'status' => 'pending',
        ]);
    }
}
