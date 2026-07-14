<?php

namespace Database\Factories;

use App\Models\UserToolAccess;
use App\Models\UserToolDevice;
use App\Models\User;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserToolDeviceFactory extends Factory
{
    protected $model = UserToolDevice::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'user_tool_access_id' => UserToolAccess::factory(),
            'tool_id' => Tool::factory(),
            'device_name' => fake()->userAgent(),
            'device_type' => fake()->randomElement(['desktop', 'mobile', 'tablet', 'browser']),
            'browser_name' => fake()->randomElement(['Chrome', 'Firefox', 'Safari']),
            'operating_system' => fake()->randomElement(['Windows', 'macOS', 'Android', 'iOS']),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'device_fingerprint' => fake()->sha256(),
            'status' => fake()->randomElement(['pending', 'active', 'removed']),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => 'pending']);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => 'active']);
    }
}
