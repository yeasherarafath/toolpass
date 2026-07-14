<?php

namespace App\Actions\Dashboard;

use App\Models\Order;
use App\Models\Payment;
use App\Models\OtpRequest;
use App\Models\UserToolAccess;
use App\Models\ToolAccount;
use App\Models\SupportTicket;
use App\Models\DeviceResetRequest;
use Illuminate\Support\Facades\Cache;

class ResolveAdminWidgets
{
    public function handle(): array
    {
        return Cache::remember('admin_dashboard_widgets', 60, function () {
            return [
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'ready_for_delivery' => Order::where('order_status', 'ready')
                    ->whereHas('userToolAccesses', fn ($q) => $q->where('delivery_status', 'pending'))
                    ->count(),
                'pending_otp' => OtpRequest::where('status', 'pending')->count(),
                'device_approvals' => DeviceResetRequest::where('status', 'pending')->count(),
                'expiring_accesses' => UserToolAccess::where('status', 'active')
                    ->whereNotNull('expires_at')
                    ->where('expires_at', '<', now()->addDays(7))
                    ->count(),
                'full_accounts' => ToolAccount::where('status', 'full')->count(),
                'open_tickets' => SupportTicket::whereNotIn('status', ['closed'])->count(),
                'revenue' => (float) Order::where('payment_status', 'paid')->sum('payable_amount'),
            ];
        });
    }
}
