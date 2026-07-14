<?php

namespace Database\Factories;

use App\Models\UserToolAccess;
use App\Models\User;
use App\Models\Tool;
use App\Models\DeviceResetRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceResetRequestFactory extends Factory
{
    protected $model = DeviceResetRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_tool_access_id' => UserToolAccess::factory(),
            'tool_id' => Tool::factory(),
            'status' => fake()->randomElement(['pending', 'completed', 'rejected']),
            'customer_reason' => fake()->sentence(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }
}
