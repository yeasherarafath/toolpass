<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Actions\Platform\AdminLoginAction;
use App\Services\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        return view('platform.admin.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        try {
            app(AdminLoginAction::class)($credentials, $request->boolean('remember'));
        } catch (\Throwable $e) {
            return back()->withErrors(['email' => $e->getMessage()])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    public function editProfile(Settings $settings)
    {
        return view('platform.admin.profile.edit', [
            'admin' => Auth::guard('admin')->user(),
        ]);
    }
}
