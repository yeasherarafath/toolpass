<?php

namespace App\Actions\Tool;

use App\Models\ToolCategory;

class DeleteToolCategoryAction
{
    public function __invoke(ToolCategory $category): void
    {
        $category->delete();
    }
}
