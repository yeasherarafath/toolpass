<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::guard('owner')->user();

        $widgets = [
            'tenants' => Tenant::count(),
            'owners' => Owner::count(),
            'plans' => Plan::where('status', 'active')->count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
        ];

        return view('platform.dashboard', compact('owner', 'widgets'));
    }
}
