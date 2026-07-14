<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PackageCustomField;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PackageCustomFieldFactory extends Factory
{
    protected $model = PackageCustomField::class;

    public function definition(): array
    {
        $name = fake()->unique()->randomElement(['invite_email', 'full_name', 'website_url', 'target_country', 'keywords']);

        return [
            'package_id' => Package::factory(),
            'label' => Str::title(str_replace('_', ' ', $name)),
            'name' => $name,
            'type' => fake()->randomElement(['text', 'email', 'number', 'textarea', 'url', 'select', 'checkbox', 'radio', 'date', 'file']),
            'placeholder' => null,
            'help_text' => null,
            'options' => null,
            'is_required' => fake()->boolean(50),
            'validation_rules' => 'required',
            'sort_order' => fake()->numberBetween(1, 5),
            'status' => 'active',
        ];
    }

    public function inviteEmail(): static
    {
        return $this->state(fn () => [
            'label' => 'Invite Email',
            'name' => 'invite_email',
            'type' => 'email',
            'is_required' => true,
            'validation_rules' => 'required|email',
        ]);
    }
}
