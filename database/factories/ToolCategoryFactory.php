<?php

namespace Database\Factories;

use App\Models\ToolCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ToolCategoryFactory extends Factory
{
    protected $model = ToolCategory::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'SEO Tools', 'AI Tools', 'Design Tools', 'Developer Tools',
            'Content Writing Tools', 'Marketing Tools', 'Productivity Tools',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(6),
            'description' => fake()->sentence(),
            'status' => 'active',
            'sort_order' => fake()->numberBetween(1, 10),
        ];
    }
}
