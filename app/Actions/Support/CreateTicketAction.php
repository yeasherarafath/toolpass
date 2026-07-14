<?php

namespace App\Actions\Support;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Str;

class CreateTicketAction
{
    public function handle(User $user, array $data): SupportTicket
    {
        $ticket = SupportTicket::create([
            'user_id' => $user->id,
            'order_id' => $data['order_id'] ?? null,
            'tool_id' => $data['tool_id'] ?? null,
            'tool_account_id' => $data['tool_account_id'] ?? null,
            'user_tool_access_id' => $data['user_tool_access_id'] ?? null,
            'ticket_number' => 'TKT-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'subject' => $data['subject'],
            'category' => $data['category'] ?? 'general',
            'priority' => $data['priority'] ?? 'medium',
            'status' => 'open',
            'assigned_to' => $data['assigned_to'] ?? null,
            'last_reply_at' => now(),
        ]);

        $ticket->messages()->create([
            'sender_id' => $user->id,
            'message' => $data['message'],
            'is_staff_reply' => false,
        ]);

        return $ticket;
    }
}
