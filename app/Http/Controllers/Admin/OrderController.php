<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\Orders\ReviewRequiredInfoAction;
use App\Actions\Payments\VerifyPaymentAction;
use App\Actions\Payments\RejectPaymentAction;
use App\Actions\Access\DeliverAccessAction;
use App\Models\Order;
use App\Models\Payment;
use App\Models\UserToolAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'package'])->latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load([
            'user',
            'package.tools',
            'package.packageCustomFields',
            'orderCustomFieldValues.packageCustomField',
            'payments.user',
            'userToolAccesses.tool',
            'userToolAccesses.toolAccount',
        ]);

        return view('admin.orders.show', compact('order'));
    }

    public function reviewInfo(Request $request, Order $order)
    {
        $validated = $request->validate([
            'decision' => ['required', 'in:approve,reject'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        app(ReviewRequiredInfoAction::class)->handle(
            $order,
            $validated['decision'],
            Auth::user(),
            $validated['reason'] ?? null
        );

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Required information ' . $validated['decision'] . 'd.');
    }

    public function verifyPayment(Payment $payment)
    {
        app(VerifyPaymentAction::class)->handle($payment, Auth::user());

        return redirect()->route('admin.orders.show', $payment->order_id)
            ->with('success', 'Payment verified.');
    }

    public function rejectPayment(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        app(RejectPaymentAction::class)->handle($payment, Auth::user(), $validated['reason'] ?? null);

        return redirect()->route('admin.orders.show', $payment->order_id)
            ->with('success', 'Payment rejected.');
    }

    public function deliverAccess(UserToolAccess $access)
    {
        app(DeliverAccessAction::class)->handle($access);

        return back()->with('success', 'Access delivered.');
    }
}
