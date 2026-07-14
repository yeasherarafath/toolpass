<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(Str::random(8)),
            'type' => fake()->randomElement(['percent', 'fixed']),
            'value' => fake()->numberBetween(5, 50),
            'min_amount' => fake()->numberBetween(0, 200),
            'max_discount' => fake()->numberBetween(50, 500),
            'currency' => 'BDT',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addDays(30),
            'usage_limit' => fake()->numberBetween(10, 100),
            'used_count' => 0,
            'status' => 'active',
        ];
    }
}
