<?php

namespace Database\Factories;

use App\Models\OtpRequest;
use App\Models\UserToolAccess;
use App\Models\User;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Factories\Factory;

class OtpRequestFactory extends Factory
{
    protected $model = OtpRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_tool_access_id' => UserToolAccess::factory(),
            'tool_id' => Tool::factory(),
            'request_type' => 'otp',
            'status' => fake()->randomElement(['pending', 'provided', 'used', 'expired', 'cancelled']),
            'customer_message' => fake()->sentence(),
            'otp_code_encrypted' => null,
            'otp_expires_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }
}
