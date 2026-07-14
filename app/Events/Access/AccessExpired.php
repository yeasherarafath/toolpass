<?php

namespace App\Events\Access;

use App\Models\UserToolAccess;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccessExpired
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public UserToolAccess $access)
    {
    }
}
