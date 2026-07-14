<?php

namespace App\Actions\Orders;

use App\Actions\Concerns\GeneratesReference;
use App\Models\Order;
use App\Models\Package;
use App\Models\User;

class CreateOrderAction
{
    use GeneratesReference;

    public function handle(User $user, Package $package, array $data = []): Order
    {
        $requiresInfo = $package->packageCustomFields()->count() > 0;

        return Order::create([
            'order_number' => $this->generateOrderNumber(),
            'user_id' => $user->id,
            'package_id' => $package->id,
            'order_status' => 'pending',
            'payment_status' => 'pending',
            'required_info_status' => $requiresInfo ? 'pending' : 'not_required',
            'amount' => $package->price,
            'payable_amount' => $package->price,
            'discount_amount' => 0,
            'currency' => $package->currency ?? config('app.currency', 'BDT'),
            'payment_method' => $data['payment_method'] ?? null,
            'starts_at' => null,
            'expires_at' => null,
            'customer_note' => $data['customer_note'] ?? null,
        ]);
    }
}
