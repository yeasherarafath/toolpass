<?php

namespace App\Actions\Concerns;

use App\Models\Coupon;
use App\Models\Order;

trait AppliesCoupon
{
    protected function computeDiscount(Coupon $coupon, float $amount): float
    {
        if ($coupon->type === 'percent') {
            $discount = $amount * ($coupon->value / 100);
            if ($coupon->max_discount !== null) {
                $discount = min($discount, (float) $coupon->max_discount);
            }
            return round($discount, 2);
        }

        return round(min((float) $coupon->value, $amount), 2);
    }

    protected function applyCouponToOrder(Coupon $coupon, Order $order): float
    {
        $amount = (float) $order->payable_amount;
        $discount = $this->computeDiscount($coupon, $amount);

        $order->coupon_id = $coupon->id;
        $order->coupon_code = $coupon->code;
        $order->discount_amount = $discount;
        $order->payable_amount = max(0, round($amount - $discount, 2));
        $order->save();

        $coupon->increment('used_count');

        return $discount;
    }
}
