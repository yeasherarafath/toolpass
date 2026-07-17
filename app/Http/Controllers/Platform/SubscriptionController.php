<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tenant;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::query()
            ->with(['owner', 'plan', 'tenant'])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('platform.admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $owners = Owner::getCached(null, 'id', ['*'], 3600);
        $plans = Plan::getCached('active', 'status', ['*'], 3600);
        $tenants = Tenant::getCached(null, 'id', ['*'], 3600);

        return view('platform.admin.subscriptions.create', compact('owners', 'plans', 'tenants'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_id' => ['required', 'exists:central.owners,id'],
            'plan_id' => ['required', 'exists:central.plans,id'],
            'tenant_id' => ['nullable', 'exists:central.tenants,id'],
            'status' => ['required', 'in:active,past_due,cancelled,expired'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'quota_enforced' => ['nullable', 'boolean'],
            'is_suspended' => ['nullable', 'boolean'],
            'suspend_reason' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $data['starts_at'] = $data['starts_at'] ?? now();
        $data['quota_enforced'] = $request->boolean('quota_enforced');
        $data['is_suspended'] = $request->boolean('is_suspended');

        Subscription::create($data);

        return redirect()->route('admin.subscriptions.index')->with('status', 'Subscription created.');
    }

    public function edit(Subscription $subscription)
    {
        $owners = Owner::getCached(null, 'id', ['*'], 3600);
        $plans = Plan::getCached(null, 'id', ['*'], 3600);
        $tenants = Tenant::getCached(null, 'id', ['*'], 3600);

        return view('platform.admin.subscriptions.edit', compact('subscription', 'owners', 'plans', 'tenants'));
    }

    public function update(Request $request, Subscription $subscription)
    {
        $data = $request->validate([
            'owner_id' => ['required', 'exists:central.owners,id'],
            'plan_id' => ['required', 'exists:central.plans,id'],
            'tenant_id' => ['nullable', 'exists:central.tenants,id'],
            'status' => ['required', 'in:active,past_due,cancelled,expired'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'quota_enforced' => ['nullable', 'boolean'],
            'is_suspended' => ['nullable', 'boolean'],
            'suspend_reason' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string'],
        ]);

        $data['quota_enforced'] = $request->boolean('quota_enforced');
        $data['is_suspended'] = $request->boolean('is_suspended');

        $subscription->update($data);

        return redirect()->route('admin.subscriptions.index')->with('status', 'Subscription updated.');
    }

    public function destroy(Subscription $subscription)
    {
        $subscription->delete();

        return redirect()->route('admin.subscriptions.index')->with('status', 'Subscription deleted.');
    }
}
