<?php

namespace App\Actions\Platform;

use App\Models\Owner;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OwnerLoginAction
{
    public function __invoke(array $credentials, bool $remember = false): Owner
    {
        if (! Auth::guard('owner')->attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'These credentials do not match our records.',
            ]);
        }

        $owner = Auth::guard('owner')->user();

        if ($owner->status !== 'active') {
            Auth::guard('owner')->logout();

            throw ValidationException::withMessages([
                'email' => 'Your account is ' . $owner->status . '.',
            ]);
        }

        $owner->forceFill(['last_login_at' => now()])->saveQuietly();

        return $owner;
    }
}
