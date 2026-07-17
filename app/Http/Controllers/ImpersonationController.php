<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Stancl\Tenancy\Features\UserImpersonation;

class ImpersonationController extends Controller
{
    public function enter(string $token)
    {
        return UserImpersonation::makeResponse($token);
    }
}
