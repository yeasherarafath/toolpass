<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'package_id' => Package::factory(),
            'rating' => fake()->numberBetween(3, 5),
            'title' => fake()->sentence(3),
            'body' => fake()->paragraph(),
            'status' => 'pending',
        ];
    }
}
