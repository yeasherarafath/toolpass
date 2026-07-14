<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Actions\Orders\CreateOrderAction;
use App\Actions\Orders\CreateRenewalAction;
use App\Actions\Payments\CreatePaymentAction;
use App\Actions\Coupon\ApplyCouponAction;
use App\Models\Order;
use App\Models\OrderCustomFieldValue;
use App\Models\Package;
use App\Models\PackageCustomField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Auth::user()->orders()->latest()->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->ensureOwned($order);

        $order->load(['package.tools', 'package.packageCustomFields', 'orderCustomFieldValues', 'payments', 'userToolAccesses.tool']);

        return view('customer.orders.show', compact('order'));
    }

    public function store(Request $request)
    {
        $package = Package::where('status', 'active')->findOrFail($request->input('package_id'));

        $data = $request->only(['payment_method', 'customer_note']);

        $order = app(CreateOrderAction::class)->handle(Auth::user(), $package, $data);

        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Order placed successfully.');
    }

    public function submitInfo(Request $request, Order $order)
    {
        $this->ensureOwned($order);

        if (! $order->package->packageCustomFields()->count()) {
            return back()->withErrors(['info' => 'This package does not require additional information.']);
        }

        $rules = [];
        foreach ($order->package->packageCustomFields as $field) {
            $rule = $field->is_required ? ['required'] : ['nullable'];
            if ($field->type === 'email') {
                $rule[] = 'email';
            }
            $rules['fields.' . $field->id] = $rule;
        }

        $validated = $request->validate($rules);

        foreach ($order->package->packageCustomFields as $field) {
            OrderCustomFieldValue::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'package_custom_field_id' => $field->id,
                ],
                [
                    'field_name' => $field->name,
                    'field_label' => $field->label,
                    'value' => $validated['fields'][$field->id] ?? null,
                ]
            );
        }

        $order->required_info_status = 'submitted';
        $order->required_info_submitted_at = now();
        $order->save();

        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Information submitted. Awaiting review.');
    }

    public function storePayment(Request $request, Order $order)
    {
        $this->ensureOwned($order);

        if ($order->payment_status === 'paid') {
            return back()->withErrors(['payment' => 'This order is already paid.']);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'method' => ['required', 'string', 'in:bkash,nagad,rocket,card,bank'],
            'sender_number' => ['nullable', 'string', 'max:30'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        app(CreatePaymentAction::class)->handle($order, Auth::user(), $validated);

        return redirect()->route('customer.orders.show', $order)
            ->with('success', 'Payment submitted. Awaiting verification.');
    }

    public function applyCoupon(Request $request, Order $order)
    {
        $this->ensureOwned($order);

        if ($order->payment_status === 'paid') {
            return back()->withErrors(['coupon' => 'Cannot apply coupon to a paid order.']);
        }

        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        try {
            app(ApplyCouponAction::class)->handle($order, $validated['code'], Auth::user());
        } catch (\Throwable $e) {
            return back()->withErrors(['coupon' => $e->getMessage()]);
        }

        return back()->with('success', 'Coupon applied.');
    }

    public function renew(Request $request, Order $order)
    {
        $this->ensureOwned($order);

        if (! $order->is_trial) {
            return back()->withErrors(['renew' => 'Only trial orders can be renewed.']);
        }

        $renewal = app(CreateRenewalAction::class)->handle($order, Auth::user());

        return redirect()->route('customer.orders.show', $renewal)
            ->with('success', 'Renewal order created.');
    }

    protected function ensureOwned(Order $order): void
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
