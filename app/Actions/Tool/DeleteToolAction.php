<?php

namespace App\Actions\Tool;

use App\Models\Tool;

class DeleteToolAction
{
    public function __invoke(Tool $tool): void
    {
        $tool->delete();
    }
}
