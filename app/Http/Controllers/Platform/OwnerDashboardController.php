<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Enum\CacheKeyEnum;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::guard('owner')->user();

        $tenant = $owner->tenant;

        $widgets = Cache::remember(
            CacheKeyEnum::OWNER_DASHBOARD_WIDGETS->value.':'.$owner->getKey(),
            60,
            function () use ($owner) {
                return [
                    'plans' => Plan::where('status', 'active')->count(),
                    'active_subscriptions' => Subscription::where('status', 'active')
                        ->where('owner_id', $owner->getKey())
                        ->count(),
                ];
            }
        );

        return view('platform.dashboard', compact('owner', 'widgets', 'tenant'));
    }
}
