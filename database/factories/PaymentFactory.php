<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        $amount = fake()->numberBetween(100, 1000);

        return [
            'order_id' => Order::factory(),
            'user_id' => User::factory(),
            'amount' => $amount,
            'currency' => 'BDT',
            'method' => fake()->randomElement(['bkash', 'nagad', 'rocket', 'card', 'bank']),
            'transaction_id' => 'PAY-' . fake()->unique()->numerify('#######'),
            'sender_number' => fake()->numerify('01#########'),
            'screenshot' => null,
            'status' => fake()->randomElement(['pending', 'verified', 'rejected']),
            'verified_by' => null,
            'verified_at' => null,
            'reject_reason' => null,
            'note' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function verified(): static
    {
        return $this->state(fn () => [
            'status' => 'verified',
            'verified_at' => now(),
        ]);
    }
}
