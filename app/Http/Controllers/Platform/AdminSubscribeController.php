<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Actions\Platform\AdminSubscribeAction;
use App\Models\Owner;
use App\Models\Plan;
use Illuminate\Http\Request;

class AdminSubscribeController extends Controller
{
    public function create()
    {
        $owners = Owner::orderBy('name')->get();
        $plans = Plan::where('status', 'active')->orderBy('sort_order')->get();

        return view('platform.admin.subscribe.create', compact('owners', 'plans'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'owner_id' => ['nullable', 'exists:central.owners,id'],
            'name' => ['required_without:owner_id', 'string', 'max:150'],
            'email' => ['required_without:owner_id', 'email', 'max:190', 'unique:central.owners,email'],
            'password' => ['required_without:owner_id', 'string', 'min:8'],
            'business_name' => ['nullable', 'string', 'max:190'],
            'slug' => ['required_without:owner_id', 'string', 'max:60', 'alpha_dash'],
            'plan_id' => ['required', 'exists:central.plans,id'],
            'status' => ['required', 'in:active,past_due,cancelled,expired'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        try {
            $subscription = app(AdminSubscribeAction::class)($data);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return redirect()->route('admin.subscriptions.edit', $subscription)->with('status', 'Subscription created.');
    }
}
