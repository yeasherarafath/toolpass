<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->numerify('01#########'),
            'password' => Hash::make('password'),
            'role' => 'customer',
            'status' => 'active',
            'avatar' => null,
            'email_verified_at' => now(),
            'notes' => null,
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => [
            'role' => 'admin',
            'email' => 'admin@example.com',
            'name' => 'Site Admin',
        ]);
    }

    public function staff(): static
    {
        return $this->state(fn () => ['role' => 'staff']);
    }
}
