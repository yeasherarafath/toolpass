<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Tool;
use App\Models\User;
use App\Models\UserToolAccess;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserToolAccessFactory extends Factory
{
    protected $model = UserToolAccess::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_id' => Order::factory(),
            'tool_id' => Tool::factory(),
            'tool_account_id' => null,
            'source' => 'purchase',
            'status' => fake()->randomElement(['pending', 'active', 'expired', 'revoked']),
            'delivery_status' => fake()->randomElement(['pending', 'delivered', 'failed']),
            'customer_email_for_invite' => fake()->safeEmail(),
            'starts_at' => now(),
            'expires_at' => now()->addDays(30),
            'invited_at' => null,
            'delivered_at' => null,
            'access_note' => null,
            'delivery_note' => null,
            'internal_note' => null,
            'last_accessed_at' => null,
            'revoked_at' => null,
            'revoked_by' => null,
            'revoked_reason' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status' => 'pending',
            'delivery_status' => 'pending',
        ]);
    }
}
