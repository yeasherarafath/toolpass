<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $amount = fake()->numberBetween(100, 1000);

        return [
            'user_id' => User::factory(),
            'package_id' => Package::factory(),
            'order_number' => 'ORD-' . fake()->unique()->numerify('#######'),
            'amount' => $amount,
            'discount_amount' => 0,
            'payable_amount' => $amount,
            'currency' => 'BDT',
            'wallet_amount' => 0,
            'paid_via_wallet' => false,
            'is_trial' => false,
            'payment_method' => fake()->randomElement(['bkash', 'nagad', 'rocket', 'bank', 'manual', 'card']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'order_status' => fake()->randomElement(['pending', 'active', 'expired', 'cancelled']),
            'required_info_status' => fake()->randomElement(['not_required', 'pending', 'submitted', 'approved', 'rejected']),
            'starts_at' => null,
            'expires_at' => null,
            'customer_note' => null,
            'admin_note' => null,
        ];
    }
}
