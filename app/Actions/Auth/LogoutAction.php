<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    public function __invoke(): void
    {
        Auth::logout();
    }
}
