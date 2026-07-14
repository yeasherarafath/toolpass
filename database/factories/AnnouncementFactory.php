<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'message' => fake()->sentence(),
            'type' => fake()->randomElement(['info', 'warning', 'success']),
            'status' => 'active',
            'visible_to' => fake()->randomElement(['all', 'customers', 'staff', 'admins']),
            'starts_at' => now(),
            'ends_at' => null,
            'created_by' => User::factory(),
        ];
    }
}
