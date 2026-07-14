<?php

namespace Database\Factories;

use App\Models\AdminTask;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AdminTaskFactory extends Factory
{
    protected $model = AdminTask::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->sentence(),
            'type' => fake()->randomElement([
                'verify_payment', 'review_required_info', 'invite_user',
                'manual_delivery', 'provide_otp', 'provide_2fa',
                'approve_device', 'device_reset', 'renewal', 'support',
            ]),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['open', 'in_progress', 'completed', 'cancelled']),
            'assigned_to' => null,
        ];
    }

    public function open(): static
    {
        return $this->state(fn () => ['status' => 'open']);
    }
}
