<?php

namespace App\Actions\Orders;

use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Str;

class CreateRenewalAction
{
    public function handle(Order $trialOrder, User $user): Order
    {
        if (! $trialOrder->is_trial) {
            throw new \RuntimeException('Only trial orders can be renewed.');
        }

        $package = $trialOrder->package;

        return Order::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'order_number' => 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'amount' => $package->price,
            'discount_amount' => 0,
            'payable_amount' => $package->price,
            'currency' => $package->currency ?? config('app.currency', 'BDT'),
            'payment_method' => null,
            'payment_status' => 'pending',
            'order_status' => 'pending',
            'required_info_status' => 'not_required',
            'is_trial' => false,
            'renewed_from_order_id' => $trialOrder->id,
        ]);
    }
}
