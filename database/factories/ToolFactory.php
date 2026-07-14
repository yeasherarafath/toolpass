<?php

namespace Database\Factories;

use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ToolFactory extends Factory
{
    protected $model = Tool::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'ChatGPT', 'Claude', 'Gemini', 'Ahrefs', 'Moz', 'Semrush',
            'Canva', 'Grammarly', 'QuillBot', 'Notion', 'Freepik', 'Envato',
        ]);

        return [
            'category_id' => ToolCategory::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'website_url' => fake()->url(),
            'logo' => null,
            'description' => fake()->sentence(),
            'status' => 'active',
            'access_type' => fake()->randomElement(['credential', 'invite', 'instruction', 'manual', 'external']),
            'otp_required' => fake()->boolean(30),
            'otp_type' => fake()->randomElement([null, 'email', 'sms', 'authenticator', 'backup_code', 'device_approval', 'manual']),
            'otp_note' => null,
            'device_restriction_enabled' => fake()->boolean(30),
            'device_limit_type' => fake()->randomElement([null, 'device', 'browser', 'session', 'ip', 'simultaneous_user', 'none']),
            'default_max_devices' => fake()->numberBetween(1, 5),
            'device_policy_note' => null,
        ];
    }

    public function credential(): static
    {
        return $this->state(fn () => ['access_type' => 'credential']);
    }

    public function invite(): static
    {
        return $this->state(fn () => ['access_type' => 'invite']);
    }

    public function manual(): static
    {
        return $this->state(fn () => ['access_type' => 'manual']);
    }
}
