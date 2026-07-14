<?php

namespace App\Actions\Dashboard;

use App\Models\Announcement;
use App\Models\SupportTicket;
use App\Models\UserToolAccess;
use Illuminate\Support\Facades\Cache;

class ResolveCustomerWidgets
{
    public function handle(int $userId): array
    {
        return Cache::remember("customer_dashboard_{$userId}", 60, function () use ($userId) {
            return [
                'delivered_accesses' => UserToolAccess::where('user_id', $userId)
                    ->where('delivery_status', 'delivered')
                    ->with('tool')
                    ->get(),
                'pending_accesses' => UserToolAccess::where('user_id', $userId)
                    ->where('delivery_status', 'pending')
                    ->with('tool')
                    ->get(),
                'open_tickets' => SupportTicket::where('user_id', $userId)
                    ->where('status', '!=', 'closed')
                    ->count(),
                'announcements' => Announcement::where('status', 'active')
                    ->whereIn('visible_to', ['all', 'customers'])
                    ->where(function ($q) {
                        $q->whereNull('ends_at')->orWhere('ends_at', '>', now());
                    })
                    ->latest()
                    ->get(),
            ];
        });
    }
}
