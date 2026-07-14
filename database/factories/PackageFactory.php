<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PackageFactory extends Factory
{
    protected $model = Package::class;

    public function definition(): array
    {
        $name = fake()->randomElement([
            'ChatGPT Pro Access - 30 Days',
            'Canva Team Invite - 30 Days',
            'Ahrefs Access - 7 Days',
            'SEO Bundle - 30 Days',
            'Creator Bundle - 30 Days',
            'Developer AI Bundle - 30 Days',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'type' => fake()->randomElement(['single', 'bundle']),
            'delivery_type' => fake()->randomElement(['auto', 'manual', 'mixed']),
            'description' => fake()->sentence(),
            'price' => fake()->numberBetween(100, 1000),
            'duration_days' => 30,
            'status' => 'active',
            'sort_order' => fake()->numberBetween(1, 10),
            'is_featured' => fake()->boolean(20),
            'currency' => 'BDT',
            'is_trial' => false,
            'trial_days' => null,
            'meta_title' => null,
            'meta_description' => null,
        ];
    }

    public function single(): static
    {
        return $this->state(fn () => ['type' => 'single']);
    }

    public function bundle(): static
    {
        return $this->state(fn () => ['type' => 'bundle']);
    }

    public function mixed(): static
    {
        return $this->state(fn () => ['delivery_type' => 'mixed']);
    }
}
