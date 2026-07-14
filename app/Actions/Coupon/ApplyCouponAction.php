<?php

namespace App\Actions\Coupon;

use App\Actions\Concerns\AppliesCoupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;

class ApplyCouponAction
{
    use AppliesCoupon;

    public function handle(Order $order, string $code, ?User $user = null): float
    {
        $coupon = app(ValidateCouponAction::class)->handle($code, $order, $user);

        $discount = $this->applyCouponToOrder($coupon, $order);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'order_id' => $order->id,
            'user_id' => $user?->id,
            'discount_amount' => $discount,
        ]);

        return $discount;
    }
}
