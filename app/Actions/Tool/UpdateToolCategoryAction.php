<?php

namespace App\Actions\Tool;

use App\Models\ToolCategory;
use App\Actions\Concerns\GeneratesUniqueSlug;

class UpdateToolCategoryAction
{
    use GeneratesUniqueSlug;

    public function __invoke(ToolCategory $category, array $data): ToolCategory
    {
        $data['slug'] = $this->uniqueSlug(ToolCategory::class, $data['slug'] ?? '', $data['name'], $category->id);

        $category->update($data);

        return $category;
    }
}
