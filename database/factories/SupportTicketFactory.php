<?php

namespace Database\Factories;

use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SupportTicketFactory extends Factory
{
    protected $model = SupportTicket::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'ticket_number' => 'TKT-' . fake()->unique()->numerify('#######'),
            'subject' => fake()->sentence(4),
            'category' => fake()->randomElement(['general', 'billing', 'technical', 'access']),
            'priority' => fake()->randomElement(['low', 'medium', 'high', 'urgent']),
            'status' => fake()->randomElement(['open', 'pending_customer', 'closed']),
            'assigned_to' => null,
            'last_reply_at' => now(),
        ];
    }
}
