<?php

namespace App\Actions\Tool;

use App\Models\Tool;
use App\Actions\Concerns\GeneratesUniqueSlug;

class UpdateToolAction
{
    use GeneratesUniqueSlug;

    public function __invoke(Tool $tool, array $data): Tool
    {
        $data['slug'] = $this->uniqueSlug(Tool::class, $data['slug'] ?? '', $data['name'], $tool->id);

        $tool->update($data);

        return $tool;
    }
}
