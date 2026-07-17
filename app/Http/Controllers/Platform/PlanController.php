<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::getCached(null, 'id', ['*'], 3600);

        return view('platform.admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('platform.admin.plans.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash', 'unique:central.plans,slug'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_cycle' => ['required', 'in:monthly,yearly,lifetime'],
            'max_staff' => ['nullable', 'integer', 'min:0'],
            'max_customers' => ['nullable', 'integer', 'min:0'],
            'max_packages' => ['nullable', 'integer', 'min:0'],
            'email_quota' => ['nullable', 'integer', 'min:0'],
            'sms_quota' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('status', 'Plan created.');
    }

    public function edit(Plan $plan)
    {
        return view('platform.admin.plans.edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['required', 'string', 'max:190', 'alpha_dash', 'unique:central.plans,slug,' . $plan->getKey()],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
            'billing_cycle' => ['required', 'in:monthly,yearly,lifetime'],
            'max_staff' => ['nullable', 'integer', 'min:0'],
            'max_customers' => ['nullable', 'integer', 'min:0'],
            'max_packages' => ['nullable', 'integer', 'min:0'],
            'email_quota' => ['nullable', 'integer', 'min:0'],
            'sms_quota' => ['nullable', 'integer', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('status', 'Plan updated.');
    }

    public function destroy(Plan $plan)
    {
        $plan->delete();

        return redirect()->route('admin.plans.index')->with('status', 'Plan deleted.');
    }
}
