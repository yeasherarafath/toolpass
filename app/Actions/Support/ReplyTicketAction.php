<?php

namespace App\Actions\Support;

use App\Models\SupportTicket;
use App\Models\User;

class ReplyTicketAction
{
    public function handle(SupportTicket $ticket, User $user, string $message, bool $isStaff = false): SupportTicket
    {
        if ($ticket->status === 'closed') {
            throw new \RuntimeException('Cannot reply to a closed ticket.');
        }

        $ticket->messages()->create([
            'sender_id' => $user->id,
            'message' => $message,
            'is_staff_reply' => $isStaff,
        ]);

        $ticket->last_reply_at = now();
        if ($isStaff && $ticket->status === 'open') {
            $ticket->status = 'pending_customer';
        }
        $ticket->save();

        return $ticket;
    }
}
