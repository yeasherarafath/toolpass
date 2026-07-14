<?php

namespace App\Actions\Tool;

use App\Models\ToolCategory;
use App\Actions\Concerns\GeneratesUniqueSlug;

class CreateToolCategoryAction
{
    use GeneratesUniqueSlug;

    public function __invoke(array $data): ToolCategory
    {
        $data['slug'] = $this->uniqueSlug(ToolCategory::class, $data['slug'] ?? '', $data['name']);

        return ToolCategory::create($data);
    }
}
