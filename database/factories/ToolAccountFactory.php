<?php

namespace Database\Factories;

use App\Models\Tool;
use App\Models\ToolAccount;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

class ToolAccountFactory extends Factory
{
    protected $model = ToolAccount::class;

    public function definition(): array
    {
        return [
            'tool_id' => Tool::factory(),
            'name' => fake()->randomElement(['ChatGPT Pro Account', 'Canva Team', 'Ahrefs Account', 'Semrush Account']) . ' ' . fake()->numberBetween(1, 9),
            'login_email' => fake()->safeEmail(),
            'login_password_encrypted' => Crypt::encrypt('secret-' . Str::random(8)),
            'recovery_email' => fake()->safeEmail(),
            'account_url' => fake()->url(),
            'subscription_type' => 'monthly',
            'purchase_price' => fake()->numberBetween(5, 50),
            'renewal_price' => fake()->numberBetween(5, 50),
            'purchase_date' => now()->subDays(10),
            'renewal_date' => now()->addDays(20),
            'expires_at' => now()->addDays(25),
            'max_users' => 5,
            'used_slots' => 0,
            'device_restriction_enabled' => fake()->boolean(30),
            'max_devices' => fake()->numberBetween(1, 5),
            'used_devices' => 0,
            'device_limit_type' => 'device',
            'allow_device_reset' => true,
            'device_reset_interval_days' => 7,
            'device_policy_note' => null,
            'otp_required' => fake()->boolean(30),
            'otp_type' => fake()->randomElement([null, 'email', 'sms']),
            'otp_receiver' => fake()->safeEmail(),
            'otp_note' => null,
            'two_factor_secret_encrypted' => null,
            'backup_codes_encrypted' => null,
            'status' => 'active',
            'last_checked_at' => null,
            'last_issue_at' => null,
            'issue_note' => null,
            'notes' => null,
        ];
    }
}
