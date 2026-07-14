<?php

namespace App\Events\ToolAccount;

use App\Models\ToolAccount;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ToolAccountCreated
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public ToolAccount $toolAccount)
    {
    }
}
