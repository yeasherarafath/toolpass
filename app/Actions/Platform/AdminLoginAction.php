<?php

namespace App\Actions\Platform;

use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AdminLoginAction
{
    public function __invoke(array $credentials, bool $remember = false): Admin
    {
        if (! Auth::guard('admin')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $admin = Auth::guard('admin')->user();

        if ($admin->status !== 'active') {
            Auth::guard('admin')->logout();

            throw ValidationException::withMessages([
                'email' => 'Your account is ' . $admin->status . '.',
            ]);
        }

        $admin->forceFill(['last_login_at' => now()])->saveQuietly();

        return $admin;
    }
}
