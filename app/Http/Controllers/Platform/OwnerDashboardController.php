<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class OwnerDashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::guard('owner')->user();

        $tenant = $owner->tenant;

        $widgets = [
            'plans' => Plan::where('status', 'active')->count(),
            'active_subscriptions' => Subscription::where('status', 'active')
                ->where('owner_id', $owner->getKey())
                ->count(),
        ];

        return view('platform.owner.dashboard', compact('owner', 'widgets', 'tenant'));
    }
}
