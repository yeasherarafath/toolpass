<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Database\Eloquent\Factories\Factory;

class WalletFactory extends Factory
{
    protected $model = Wallet::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'currency' => 'BDT',
            'balance' => fake()->numberBetween(0, 5000),
            'locked_balance' => 0,
        ];
    }
}
