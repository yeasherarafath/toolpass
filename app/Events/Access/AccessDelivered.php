<?php

namespace App\Events\Access;

use App\Models\UserToolAccess;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AccessDelivered
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public UserToolAccess $access)
    {
    }
}
