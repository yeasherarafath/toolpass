<?php

namespace App\Actions\ToolAccount;

use App\Models\ToolAccount;

class DeleteToolAccountAction
{
    public function __invoke(ToolAccount $account): void
    {
        $account->delete();
    }
}
