<?php

namespace Database\Factories;

use App\Models\Package;
use App\Models\PackageTool;
use App\Models\Tool;
use Illuminate\Database\Eloquent\Factories\Factory;

class PackageToolFactory extends Factory
{
    protected $model = PackageTool::class;

    public function definition(): array
    {
        return [
            'package_id' => Package::factory(),
            'tool_id' => Tool::factory(),
        ];
    }
}
