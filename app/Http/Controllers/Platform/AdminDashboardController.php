<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Enum\CacheKeyEnum;
use App\Models\Admin;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $admin = Auth::guard('admin')->user();

        $widgets = Cache::remember(CacheKeyEnum::ADMIN_DASHBOARD_WIDGETS->value, 60, function () {
            return [
                'admins' => Admin::count(),
                'owners' => Owner::count(),
                'tenants' => Tenant::count(),
                'plans' => Plan::where('status', 'active')->count(),
                'subscriptions' => Subscription::count(),
                'active_subscriptions' => Subscription::where('status', 'active')->count(),
            ];
        });

        return view('platform.admin.dashboard', compact('admin', 'widgets'));
    }
}
