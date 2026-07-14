<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;

class LoginAction
{
    public function __invoke(array $credentials, bool $remember = false): User
    {
        if (! Auth::attempt($credentials, $remember)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $user = Auth::user();

        if ($user->status !== 'active') {
            Auth::logout();
            throw new \RuntimeException('Your account is ' . $user->status . '.');
        }

        return $user;
    }
}
