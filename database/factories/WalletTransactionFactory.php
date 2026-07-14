<?php

namespace Database\Factories;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletTransactionFactory extends Factory
{
    protected $model = WalletTransaction::class;

    public function definition(): array
    {
        return [
            'wallet_id' => Wallet::factory(),
            'type' => fake()->randomElement(['credit', 'debit']),
            'amount' => fake()->randomFloat(2, 1, 100),
            'balance_after' => fake()->randomFloat(2, 0, 200),
            'ref_type' => null,
            'ref_id' => null,
            'note' => fake()->sentence(),
        ];
    }
}
