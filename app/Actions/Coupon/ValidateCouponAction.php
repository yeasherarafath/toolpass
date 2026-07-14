<?php

namespace App\Actions\Coupon;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;

class ValidateCouponAction
{
    public function handle(string $code, Order $order, ?User $user = null): Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        if (! $coupon || $coupon->status !== 'active') {
            throw new \RuntimeException('Invalid or inactive coupon code.');
        }

        $now = now();
        if ($coupon->starts_at && $coupon->starts_at->gt($now)) {
            throw new \RuntimeException('Coupon is not active yet.');
        }
        if ($coupon->ends_at && $coupon->ends_at->lt($now)) {
            throw new \RuntimeException('Coupon has expired.');
        }

        if ($coupon->min_amount !== null && (float) $order->payable_amount < (float) $coupon->min_amount) {
            throw new \RuntimeException('Order does not meet the minimum amount for this coupon.');
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            throw new \RuntimeException('Coupon usage limit reached.');
        }

        if ($user && $coupon->couponUsages()->where('user_id', $user->id)->exists()) {
            throw new \RuntimeException('You have already used this coupon.');
        }

        return $coupon;
    }
}
