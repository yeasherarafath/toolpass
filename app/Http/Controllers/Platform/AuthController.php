<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Actions\Platform\OwnerLoginAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('platform.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            app(OwnerLoginAction::class)($credentials, $request->boolean('remember'));
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => $e->getMessage()])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('platform.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('platform.login');
    }
}
