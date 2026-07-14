<?php

namespace App\Actions\Tool;

use App\Models\Tool;
use App\Actions\Concerns\GeneratesUniqueSlug;

class CreateToolAction
{
    use GeneratesUniqueSlug;

    public function __invoke(array $data): Tool
    {
        $data['slug'] = $this->uniqueSlug(Tool::class, $data['slug'] ?? '', $data['name']);

        return Tool::create($data);
    }
}
